// --- DOM Elements ---
const chartAnswer = document.getElementById('chartAnwser');
const quizSelect = document.getElementById('quizSelect');
const userSelect = document.querySelector('.chart2 #userSelect');
const chartScore = document.querySelector('.chart2 #chartScore');
const userListTbody = document.getElementById('userList');
const groupListTbody = document.getElementById('groupList');
const addGroupBtn = document.getElementById('addGroupBtn');
const groupNameInput = document.getElementById('groupName');
const addUserBtn = document.getElementById('addUserBtn');
const quizNameDisplay = document.getElementById('quizName');
let cachedAllScores = null;
let cachedAllUsers = null;
let cachedAllGroups = null;
let chartPrincipal = null;
let chartUserScore = null;

// --- Configuration ---
const LAST_DAYS_TO_SHOW = 7;

// --- Utility Functions ---

/**
 * Calculates the average score for each questionnaire from a list of all scores.
 * @param {Array|null} allScoresData - Array of score objects for all users.
 * Each object should have at least { questionnaire_name: string, score: number }.
 * @returns {Map<string, number>} - A Map where keys are questionnaire names
 * and values are the average scores for that questionnaire.
 */
function calculateQuestionnaireAverages(allScoresData) {
	const averages = new Map();
	if (!allScoresData || allScoresData.length === 0) {
		return averages; // Return empty map if no data
	}

	const scoreSums = new Map(); // Map<qName, { totalScore: number, count: number }>

	allScoresData.forEach((score) => {
		const qName = score.questionnaire_name;
		const currentScore = typeof score.score === 'number' ? score.score : 0; // Ensure score is a number

		if (!scoreSums.has(qName)) {
			scoreSums.set(qName, { totalScore: 0, count: 0 });
		}

		const entry = scoreSums.get(qName);
		entry.totalScore += currentScore;
		entry.count++;
	});

	// Calculate final averages
	scoreSums.forEach((data, qName) => {
		const average = data.count > 0 ? data.totalScore / data.count : 0;
		averages.set(qName, average);
	});

	return averages;
}

/**
 * Helper function for making API calls using fetch.
 * @param {string} url - The API endpoint URL.
 * @param {object} [options={}] - Fetch options (method, headers, body, etc.).
 * @returns {Promise<any>} - Resolves with the data.success part of the response or rejects with an error.
 */
async function apiFetch(url, options = {}) {
	loadingSpinner();
	try {
		const response = await fetch(url, options);
		const data = await response.json();

		if (!response.ok || !data || (typeof data.success === 'undefined' && typeof data.error === 'undefined')) {
			// Handle cases where response is ok but JSON structure is unexpected
			console.error('API Error - Unexpected response format:', data);
			throw new Error(data?.error || `Request failed with status ${response.status}`);
		}

		if (data.success) {
			return data.success;
		} else {
			throw new Error(data.error || 'An unknown API error occurred.');
		}
	} catch (error) {
		console.error(`API Fetch Error (${url}):`, error);
		// Ensure error is an instance of Error for consistent handling
		const errorMessage = error instanceof Error ? error.message : String(error);
		popUp('red', 5000, 'API Error', errorMessage);
		throw error; // Re-throw to be caught by calling function if needed
	} finally {
		removeLoadingSpinner();
	}
}

// --- Data Fetching Functions ---

async function getAllScores() {
	if (cachedAllScores) {
		return cachedAllScores;
	}
	try {
		cachedAllScores = await apiFetch(relativePath + '/api/questionnaires/scores');
		return cachedAllScores;
	} catch (error) {
		return null; // Let caller handle null
	}
}

async function getAllUsers() {
	if (cachedAllUsers) {
		return cachedAllUsers;
	}
	try {
		cachedAllUsers = await apiFetch(relativePath + '/api/users');
		return cachedAllUsers;
	} catch (error) {
		cachedAllUsers = null; // Clear cache on error
		return null;
	}
}

async function getAllGroups() {
	if (cachedAllGroups) {
		return cachedAllGroups;
	}
	try {
		// Add a default "Aucun" group client-side for consistency
		const groups = (await apiFetch(relativePath + '/api/group')) || [];
		cachedAllGroups = [...groups, { id: 0, nom: 'Aucun' }]; // Add default *after* fetch
		return cachedAllGroups;
	} catch (error) {
		cachedAllGroups = [{ id: 0, nom: 'Aucun' }]; // Provide default even on error
		return cachedAllGroups;
	}
}

async function getUserScores(userId) {
	try {
		const scores = await apiFetch(relativePath + `/api/users/${userId}/scores`);
		// The apiFetch helper already ensures we get the 'success' part or throws.
		// We might still want to check if it's an array here for robustness.
		if (!Array.isArray(scores)) {
			throw new Error('User scores data is not in the expected array format.');
		}
		return scores;
	} catch (error) {
		// Error already shown by apiFetch, just return null or empty array
		return null;
	}
}

// --- Charting Functions ---

/**
 * Generates an array of dates for the last N days.
 * @param {number} numberOfDays - How many days back to include.
 * @returns {string[]} - Array of dates in 'YYYY-MM-DD' format.
 */
function getLastNDays(numberOfDays) {
	const dates = [];
	for (let i = numberOfDays - 1; i >= 0; i--) {
		const date = new Date();
		date.setDate(date.getDate() - i);
		dates.push(date.toISOString().split('T')[0]);
	}
	return dates;
}

/**
 * Calculates the number of answers per day for a given period.
 * @param {Array} allScoresData - The array of score objects.
 * @param {string[]} dateRange - Array of dates ('YYYY-MM-DD') to count answers for.
 * @param {string|null} [filterQuizName=null] - Optional quiz name to filter by.
 * @returns {number[]} - Array containing the count of answers for each day in dateRange.
 */
function calculateDailyAnswers(allScoresData, dateRange, filterQuizName = null) {
	if (!allScoresData) return dateRange.map(() => 0); // Return zeros if no data

	return dateRange.map((day) => {
		const targetDateStr = new Date(day).toLocaleDateString();
		let count = 0;
		for (const score of allScoresData) {
			const scoreDateStr = new Date(score.date.split(' ')[0]).toLocaleDateString();
			if (scoreDateStr === targetDateStr) {
				if (!filterQuizName || score.questionnaire_name === filterQuizName) {
					count++;
				}
			}
		}
		return count;
	});
}

async function renderMainChart() {
	const allScoresData = await getAllScores();
	if (!allScoresData) {
		chartAnswer.innerHTML = '<p>Could not load score data for the chart.</p>';
		return;
	}

	const last7Days = getLastNDays(LAST_DAYS_TO_SHOW);
	const initialDailyAnswers = calculateDailyAnswers(allScoresData, last7Days);

	quizSelect.innerHTML = '';
	const uniqueQuizNames = [...new Set(allScoresData.map((score) => score.questionnaire_name))];
	quizSelect.innerHTML += `<option value="all">Tous les questionnaires</option>`;
	uniqueQuizNames.forEach((name) => {
		quizSelect.innerHTML += `<option value="${name}">${name}</option>`;
	});
	quizNameDisplay.textContent = 'Tous les questionnaires';

	const options = {
		chart: { type: 'line', height: 350 },
		series: [{ name: 'Réponses', data: initialDailyAnswers }],
		xaxis: { categories: last7Days.map((date) => new Date(date).toLocaleDateString()) },
		stroke: { curve: 'smooth' },
		markers: { size: 4 },
		tooltip: { x: { format: 'dd/MM/yyyy' } },
	};

	if (!chartPrincipal) {
		chartPrincipal = new ApexCharts(chartAnswer, options);
		chartPrincipal.render();
	} else {
		chartPrincipal.updateOptions(options);
	}
}

async function updateMainChart() {
	const selectedQuizValue = quizSelect.value;
	const selectedQuizName = quizSelect.options[quizSelect.selectedIndex].text;
	quizNameDisplay.textContent = selectedQuizName;

	const allScoresData = cachedAllScores;
	if (!allScoresData) {
		console.error('Cannot update chart, score data not available.');
		return;
	}

	const last7Days = getLastNDays(LAST_DAYS_TO_SHOW);
	const filterName = selectedQuizValue === 'all' ? null : selectedQuizValue;
	const newDailyAnswers = calculateDailyAnswers(allScoresData, last7Days, filterName);

	if (chartPrincipal) {
		chartPrincipal.updateSeries([{ data: newDailyAnswers }]);
	} else {
		console.error('Main chart not initialized.');
	}
}

async function renderUserScoreChart(userId) {
	if (chartUserScore) {
		chartUserScore.destroy();
		chartUserScore = null;
	}
	chartScore.innerHTML = '';
	loadingSpinner();

	try {
		const [userScoreData, allScoresData] = await Promise.all([getUserScores(userId), getAllScores()]);

		if (!userScoreData) {
			chartScore.innerHTML = '<p>Could not fetch scores for this user.</p>';
			return;
		}

		if (userScoreData.length === 0) {
			chartScore.innerHTML = '<p>Aucun score disponible pour cet utilisateur.</p>';
			return;
		}

		const classAveragesMap = calculateQuestionnaireAverages(allScoresData);

		const labels = [];
		const userScores = [];
		const classAverages = [];
		const maxScores = [];
		const dates = [];

		userScoreData.forEach((score) => {
			labels.push(score.questionnaire_name);
			userScores.push(parseFloat(score.score.toFixed(1)));
			maxScores.push(score.score_on);
			dates.push(new Date(score.date).toLocaleDateString());
			const avg = classAveragesMap.get(score.questionnaire_name) ?? 0;
			classAverages.push(parseFloat(avg.toFixed(1)));
		});
		const options = {
			chart: {
				type: 'bar',
				height: 350,
			},
			plotOptions: {
				bar: {
					borderRadius: 4,
					horizontal: false,
				},
			},
			dataLabels: {
				enabled: false,
			},
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent'],
			},
			series: [
				{
					name: 'Score Utilisateur', // Series for the user's scores
					data: userScores,
				},
				{
					name: 'Moyenne Classe', // Series for the class averages
					data: classAverages,
				},
			],
			xaxis: {
				categories: labels, // Questionnaire names
			},
			yaxis: {
				title: {
					text: 'Score',
				},
				max: (maxVal) => Math.max(maxVal, Math.max(...maxScores)),
			},
			tooltip: {
				shared: true,
				intersect: false,
				y: {
					formatter: function (value, { seriesIndex, dataPointIndex, w }) {
						if (seriesIndex === 0) {
							const maxScore = maxScores[dataPointIndex];
							const date = dates[dataPointIndex];
							return `${value.toFixed(1)} / ${maxScore} (Le ${date})`;
						} else {
							return `${value.toFixed(1)}`;
						}
					},
				},
			},
			legend: {
				position: 'top',
				horizontalAlign: 'center',
				offsetY: -10,
			},
		};

		chartScore.innerHTML = '<div id="userScoreChartInstance"></div>';
		chartUserScore = new ApexCharts(document.querySelector('#userScoreChartInstance'), options);
		chartUserScore.render();
	} catch (error) {
		console.error('Error rendering user score chart:', error);
		chartScore.innerHTML = '<p>Une erreur est survenue lors de la génération du graphique des scores.</p>';
	} finally {
		removeLoadingSpinner();
	}
}

async function populateUserSelect() {
	const allUsers = await getAllUsers();
	userSelect.innerHTML = '<option value="0" selected>Sélectionner un utilisateur</option>'; // Default option

	if (allUsers && allUsers.length > 0) {
		allUsers.forEach((user) => {
			const option = document.createElement('option');
			option.value = user.id;
			option.textContent = user.username;
			userSelect.appendChild(option);
		});
	} else {
		userSelect.disabled = true;
		userSelect.innerHTML = '<option value="0">Aucun utilisateur trouvé</option>';
	}
}

async function populateUserListTable() {
	const [allUsers, allGroups] = await Promise.all([getAllUsers(), getAllGroups()]);

	userListTbody.innerHTML = '';

	if (!allUsers || allUsers.length === 0) {
		userListTbody.innerHTML = '<tr><td colspan="5">Aucun utilisateur trouvé.</td></tr>';
		return;
	}
	if (!allGroups) {
		popUp('red', 5000, 'Erreur', 'Impossible de charger les groupes pour la liste des utilisateurs.');
		userListTbody.innerHTML = '<tr><td colspan="5">Erreur de chargement des groupes.</td></tr>';
		return;
	}

	allUsers.forEach((user) => {
		const tr = document.createElement('tr');
		tr.setAttribute('data-user-id', user.id);

		// Username
		const tdUsername = document.createElement('td');
		tdUsername.textContent = user.username;
		tr.appendChild(tdUsername);

		// Email
		const tdEmail = document.createElement('td');
		tdEmail.textContent = user.email;
		tr.appendChild(tdEmail);

		// Group Select
		const tdGroup = document.createElement('td');
		const groupSelect = document.createElement('select');
		groupSelect.classList.add('groupChangeSelect');
		groupSelect.dataset.userId = user.id;
		const currentGroupId = user.groupe_id ?? 0;

		allGroups.forEach((group) => {
			const option = document.createElement('option');
			option.value = group.id;
			option.textContent = group.nom;
			if (group.id == currentGroupId) {
				option.selected = true;
				groupSelect.dataset.initialGroupId = group.id;
			}
			groupSelect.appendChild(option);
		});
		tdGroup.appendChild(groupSelect);
		tr.appendChild(tdGroup);

		const tdRole = document.createElement('td');
		const roleButton = document.createElement('button');
		roleButton.classList.add('actionButton', 'roleChangeButton');
		roleButton.dataset.userId = user.id;
		roleButton.dataset.currentRole = user.role;
		roleButton.textContent = user.role == '1' ? 'Admin' : 'Utilisateur';
		tdRole.appendChild(roleButton);
		tr.appendChild(tdRole);

		const tdDelete = document.createElement('td');
		const deleteButton = document.createElement('button');
		deleteButton.classList.add('actionButton', 'deleteUserButton');
		deleteButton.dataset.userId = user.id;
		deleteButton.dataset.username = user.username;
		deleteButton.textContent = 'Supprimer';
		tdDelete.appendChild(deleteButton);
		tr.appendChild(tdDelete);

		userListTbody.appendChild(tr);
	});
}

async function populateGroupListTable() {
	const allGroups = ((await getAllGroups()) || []).filter((g) => g.id !== 0);
	groupListTbody.innerHTML = '';

	if (allGroups.length === 0) {
		groupListTbody.innerHTML = '<tr><td colspan="2">Aucun groupe défini.</td></tr>';
		return;
	}

	allGroups.forEach((group) => {
		const tr = document.createElement('tr');
		tr.setAttribute('data-group-id', group.id);

		const tdName = document.createElement('td');
		tdName.textContent = group.nom;
		tr.appendChild(tdName);

		const tdAction = document.createElement('td');
		const deleteButton = document.createElement('button');
		deleteButton.classList.add('actionButton', 'deleteGroupButton');
		deleteButton.dataset.groupId = group.id;
		deleteButton.dataset.groupName = group.nom;
		deleteButton.textContent = 'Supprimer';
		tdAction.appendChild(deleteButton);
		tr.appendChild(tdAction);

		groupListTbody.appendChild(tr);
	});
}

async function handleUserRoleChange(userId, currentRole) {
	const newRole = currentRole == '1' ? '0' : '1';
	const roleName = newRole == '1' ? 'Admin' : 'Utilisateur';

	if (!confirm(`Voulez-vous vraiment changer le rôle de cet utilisateur en ${roleName} ?`)) {
		return;
	}

	try {
		const result = await apiFetch(relativePath + `/api/users/${userId}/role`, {
			method: 'PUT',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ role: newRole }),
		});
		popUp('base', 3000, 'Succès', result || 'Rôle utilisateur mis à jour.');
		const button = userListTbody.querySelector(`tr[data-user-id="${userId}"] .roleChangeButton`);
		if (button) {
			button.dataset.currentRole = newRole;
			button.textContent = roleName;
		}
		cachedAllUsers = null;
		await populateUserListTable();
	} catch (error) {
		console.error('Failed to update user role:', error);
	}
}

async function handleUserGroupChange(userId, newGroupId, selectElement) {
	const initialGroupId = selectElement.dataset.initialGroupId;
	if (newGroupId == initialGroupId) {
		console.log('Group not changed.');
		return;
	}

	const groupName = selectElement.options[selectElement.selectedIndex].text;

	if (!confirm(`Assigner cet utilisateur au groupe "${groupName}" ?`)) {
		selectElement.value = initialGroupId;
		return;
	}

	try {
		const result = await apiFetch(relativePath + `/api/group/${newGroupId}/users`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ idUser: userId }),
		});
		popUp('base', 3000, 'Succès', result || `Utilisateur assigné au groupe ${groupName}.`);
		selectElement.dataset.initialGroupId = newGroupId;
		cachedAllUsers = null;
		await populateUserListTable();
	} catch (error) {
		selectElement.value = initialGroupId;
		console.error('Failed to change user group:', error);
	}
}

async function handleUserDelete(userId, username) {
	if (!confirm(`Voulez-vous vraiment supprimer l'utilisateur "${username}" ? Cette action est irréversible.`)) {
		return;
	}

	try {
		const result = await apiFetch(relativePath + `/api/users/${userId}`, {
			method: 'DELETE',
			headers: { 'Content-Type': 'application/json' },
		});
		popUp('base', 3000, 'Succès', result || `Utilisateur ${username} supprimé.`);
		const row = userListTbody.querySelector(`tr[data-user-id="${userId}"]`);
		if (row) {
			row.remove();
		}
		cachedAllUsers = null;
		cachedAllScores = null;
		await populateUserSelect();
		await populateUserListTable();
	} catch (error) {
		console.error('Failed to delete user:', error);
	}
}

async function handleGroupDelete(groupId, groupName) {
	if (!confirm(`Voulez-vous vraiment supprimer le groupe "${groupName}" ? Les utilisateurs de ce groupe ne seront plus assignés à aucun groupe.`)) {
		return;
	}

	try {
		const result = await apiFetch(relativePath + `/api/group/${groupId}`, {
			method: 'DELETE',
			headers: { 'Content-Type': 'application/json' },
		});
		popUp('base', 3000, 'Succès', result || `Groupe ${groupName} supprimé.`);
		const row = groupListTbody.querySelector(`tr[data-group-id="${groupId}"]`);
		if (row) {
			row.remove();
		}
		cachedAllGroups = null;
		cachedAllUsers = null;
		await Promise.all([populateGroupListTable(), populateUserListTable()]);
	} catch (error) {
		console.error('Failed to delete group:', error);
	}
}

async function handleAddGroup() {
	const groupName = groupNameInput.value.trim();
	if (groupName === '') {
		popUp('red', 5000, 'Erreur', 'Veuillez entrer un nom de groupe');
		return;
	}

	try {
		const result = await apiFetch(relativePath + '/api/group', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ name: groupName }),
		});
		popUp('base', 3000, 'Succès', result || `Groupe "${groupName}" ajouté.`);
		groupNameInput.value = '';
		cachedAllGroups = null;
		await Promise.all([populateGroupListTable(), populateUserListTable()]);
	} catch (error) {
		console.error('Failed to add group:', error);
	}
}

async function openAddUserModal() {
	const existingModal = document.querySelector('.modalJavascriptAddUser');
	if (existingModal) {
		existingModal.remove();
	}

	const modal = document.createElement('div');
	modal.classList.add('modalJavascript', 'modalJavascriptAddUser');
	modal.innerHTML = `
        <div class="containerJavascript">
            <span class="close" title="Fermer">&times;</span>
            <h2>Ajouter un utilisateur</h2>
            <label for="modalUsername">Nom d'utilisateur:</label>
            <input type="text" id="modalUsername" required>
            <label for="modalEmail">Email:</label>
            <input type="email" id="modalEmail" required>
            <label for="modalGroupSelect">Groupe:</label>
            <select id="modalGroupSelect">
                </select>
            <button id="modalSubmitUserBtn">Ajouter</button>
        </div>`;
	document.body.appendChild(modal);

	const groupSelect = modal.querySelector('#modalGroupSelect');
	const allGroups = await getAllGroups();

	allGroups.forEach((group) => {
		const option = document.createElement('option');
		option.value = group.id;
		option.textContent = group.nom;
		groupSelect.appendChild(option);
	});
	groupSelect.value = '0';

	modal.querySelector('.close').addEventListener('click', () => modal.remove());
	modal.querySelector('#modalSubmitUserBtn').addEventListener('click', handleAddUserSubmit);

	modal.style.display = 'flex';
}

async function handleAddUserSubmit() {
	const modal = document.querySelector('.modalJavascriptAddUser');
	if (!modal) return;

	const username = modal.querySelector('#modalUsername').value.trim();
	const email = modal.querySelector('#modalEmail').value.trim();
	const groupId = modal.querySelector('#modalGroupSelect').value;
	const password = Math.random().toString(36).slice(-8); // Generate random password

	if (!username || !email) {
		popUp('red', 5000, 'Erreur', "Veuillez remplir le nom d'utilisateur et l'email.");
		return;
	}
	if (!email.includes('@') || !email.includes('.')) {
		popUp('red', 5000, 'Erreur', 'Veuillez entrer un email valide.');
		return;
	}

	try {
		const result = await apiFetch(relativePath + '/api/auth/register', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				username: username,
				email: email,
				password: password,
				groupID: groupId === '0' ? null : groupId, // Send null if 'Aucun' group selected
				creationAdmin: true,
			}),
		});
		popUp('base', 5000, 'Succès', result || `Utilisateur ${username} ajouté avec succès. Mot de passe temporaire: ${password}`);
		modal.remove();
		cachedAllUsers = null;
		await Promise.all([populateUserListTable(), populateUserSelect()]);
	} catch (error) {
		console.error('Failed to add user:', error);
	}
}

// --- Event Listeners Setup ---

function setupEventListeners() {
	quizSelect.addEventListener('change', updateMainChart);

	userSelect.addEventListener('change', () => {
		const userId = userSelect.value;
		if (userId === '0') {
			if (chartUserScore) chartUserScore.destroy();
			chartScore.innerHTML = '<p>Veuillez sélectionner un utilisateur.</p>';
		} else {
			renderUserScoreChart(userId);
		}
	});

	addGroupBtn.addEventListener('click', handleAddGroup);

	addUserBtn.addEventListener('click', openAddUserModal);

	userListTbody.addEventListener('click', (event) => {
		const target = event.target;
		const userId = target.dataset.userId;

		if (target.classList.contains('roleChangeButton') && userId) {
			const currentRole = target.dataset.currentRole;
			handleUserRoleChange(userId, currentRole);
		} else if (target.classList.contains('deleteUserButton') && userId) {
			const username = target.dataset.username;
			handleUserDelete(userId, username);
		}
	});

	userListTbody.addEventListener('change', (event) => {
		const target = event.target;
		if (target.classList.contains('groupChangeSelect')) {
			const userId = target.dataset.userId;
			const newGroupId = target.value;
			handleUserGroupChange(userId, newGroupId, target);
		}
	});

	groupListTbody.addEventListener('click', (event) => {
		const target = event.target;
		if (target.classList.contains('deleteGroupButton')) {
			const groupId = target.dataset.groupId;
			const groupName = target.dataset.groupName;
			if (groupId) {
				handleGroupDelete(groupId, groupName);
			}
		}
	});

	groupNameInput.addEventListener('keypress', (event) => {
		if (event.key === 'Enter') {
			event.preventDefault();
			handleAddGroup();
		}
	});
}

// --- Initialization ---

async function initializeDashboard() {
	console.log('Initializing Dashboard...');
	loadingSpinner(); // Show initial loading

	setupEventListeners();

	try {
		await Promise.all([renderMainChart(), populateUserSelect(), populateUserListTable(), populateGroupListTable()]);
		chartScore.innerHTML = '<p>Veuillez sélectionner un utilisateur.</p>';
	} catch (error) {
		console.error('Dashboard initialization failed:', error);
		document.body.innerHTML = '<h1>Error loading dashboard data. Please try again later.</h1>'; // Drastic fallback
	} finally {
		removeLoadingSpinner();
		console.log('Dashboard Initialized.');
	}
}

initializeDashboard();
