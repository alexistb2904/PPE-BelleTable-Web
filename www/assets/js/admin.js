const chartAnwser = document.getElementById('chartAnwser');
const chartScore = document.getElementById('chartScore');
const quizSelect = document.getElementById('quizSelect');
const userSelect = document.getElementById('userSelect');
let chartPrincipal = null;
let chartSecondaire = null;
let allScoreFromAllResult = null;
let last7Days = [];

function generateLast7Days() {
	last7Days = [];
	for (let i = 6; i >= 0; i--) {
		const date = new Date();
		date.setDate(date.getDate() - i);
		last7Days.push(date.toISOString().split('T')[0]);
	}
}

function countAnswersByDay(scores, questionnaireName = null) {
	const counts = new Array(7).fill(0);
	const scoresByDate = new Map();

	scores.forEach((score) => {
		const scoreDate = score.date.split(' ')[0];
		if (!scoresByDate.has(scoreDate)) {
			scoresByDate.set(scoreDate, []);
		}
		scoresByDate.set(scoreDate, [...scoresByDate.get(scoreDate), score]);
	});

	last7Days.forEach((day, index) => {
		const dayScores = scoresByDate.get(day) || [];
		counts[index] = questionnaireName ? dayScores.filter((score) => score.questionnaire_name === questionnaireName).length : dayScores.length;
	});

	return counts;
}

async function fetchWithErrorHandling(url, options = {}) {
	try {
		const response = await fetch(url, options);
		const data = await response.json();
		if (data.success) {
			return data.success;
		} else {
			popUp('red', 5000, 'Erreur', data.error);
			return null;
		}
	} catch (error) {
		popUp('red', 5000, 'Erreur', 'Erreur de connexion: ' + error.message);
		return null;
	}
}

async function generateChart() {
	loadingSpinner();
	generateLast7Days();

	const allScoreFromAll = await fetchWithErrorHandling(relativePath + '/api/questionnaires/scores');
	removeLoadingSpinner();

	if (!allScoreFromAll) return;

	allScoreFromAllResult = allScoreFromAll;

	const uniqueQuestionnaires = [...new Set(allScoreFromAll.map((score) => score.questionnaire_name))];
	const numberOfAnswersByDay = countAnswersByDay(allScoreFromAll);
	const options = {
		chart: {
			type: 'line',
			height: 350,
			animations: {
				enabled: true,
				easing: 'easeinout',
				speed: 800,
			},
		},
		series: [
			{
				name: 'Réponses',
				data: numberOfAnswersByDay,
			},
		],
		xaxis: {
			categories: last7Days.map((date) => new Date(date).toLocaleDateString('fr-FR')),
			title: {
				text: 'Jours',
			},
		},
		yaxis: {
			title: {
				text: 'Nombre de réponses',
			},
		},
		colors: ['#f1356d'],
		stroke: {
			curve: 'smooth',
		},
	};

	const optionsHtml = ['<option value="all">Tous les questionnaires</option>'].concat(uniqueQuestionnaires.map((q) => `<option value="${q}">${q}</option>`)).join('');
	quizSelect.innerHTML = optionsHtml;

	await populateUserSelect();

	chartPrincipal = new ApexCharts(chartAnwser, options);
	chartPrincipal.render();

	quizSelect.addEventListener('change', handleQuizSelectChange);
	userSelect.addEventListener('change', handleUserSelectChange);
}

function handleQuizSelectChange(e) {
	const selectedQuiz = e.target.value;
	const quizName = e.target.options[e.target.selectedIndex].text;
	document.getElementById('quizName').textContent = quizName;

	const numberOfAnswersByDay = selectedQuiz === 'all' ? countAnswersByDay(allScoreFromAllResult) : countAnswersByDay(allScoreFromAllResult, selectedQuiz);

	chartPrincipal.updateSeries([
		{
			name: 'Réponses',
			data: numberOfAnswersByDay,
		},
	]);
}

async function populateUserSelect() {
	const allUsers = await getAllUsers();
	if (!allUsers) return;

	const optionsHtml = ['<option value="all">Tous les utilisateurs</option>'].concat(allUsers.map((user) => `<option value="${user.id}">${user.username}</option>`)).join('');
	userSelect.innerHTML = optionsHtml;
	handleUserSelectChange({ target: { value: 'all' } });
}

function handleUserSelectChange(e) {
	const selectedUserId = e.target.value;

	if (selectedUserId === 'all') {
		updateUserChart(allScoreFromAllResult);
	} else {
		const userScores = allScoreFromAllResult.filter((score) => score.user_id == selectedUserId);
		updateUserChart(userScores);
	}
}

function updateUserChart(scores) {
	const userScoresByDay = countAnswersByDay(scores);

	const options = {
		chart: {
			type: 'bar',
			height: 350,
		},
		series: [
			{
				name: 'Scores',
				data: userScoresByDay,
			},
		],
		xaxis: {
			categories: last7Days.map((date) => new Date(date).toLocaleDateString('fr-FR')),
			title: {
				text: 'Jours',
			},
		},
		yaxis: {
			title: {
				text: 'Nombre de participations',
			},
		},
		colors: ['#ef4e7e'],
	};

	if (chartSecondaire) {
		chartSecondaire.destroy();
	}

	chartSecondaire = new ApexCharts(chartScore, options);
	chartSecondaire.render();
}

generateChart();

let usersCache = null;
let groupsCache = null;

async function getAllUsers() {
	if (usersCache) return usersCache;

	loadingSpinner();
	usersCache = await fetchWithErrorHandling(relativePath + '/api/users');
	removeLoadingSpinner();
	return usersCache;
}

async function getAllGroups() {
	if (groupsCache) return groupsCache;

	loadingSpinner();
	groupsCache = await fetchWithErrorHandling(relativePath + '/api/group');
	removeLoadingSpinner();
	return groupsCache;
}

// Fonction pour invalider le cache
function clearCache() {
	usersCache = null;
	groupsCache = null;
}

const userList = document.getElementById('userList');

// Event delegation pour les actions utilisateur
userList.addEventListener('click', handleUserAction);
userList.addEventListener('change', handleGroupChange);

async function handleUserAction(e) {
	if (e.target.classList.contains('actionButton')) {
		e.preventDefault();

		if (e.target.closest('.roleChange')) {
			await handleRoleChange(e.target);
		} else if (e.target.closest('.deleteUser')) {
			await handleUserDelete(e.target);
		}
	}
}

async function handleGroupChange(e) {
	if (e.target.classList.contains('groupChangeSelect')) {
		await handleUserGroupChange(e.target);
	}
}

async function handleRoleChange(button) {
	const userId = button.getAttribute('data-userID');
	const userRole = button.getAttribute('data-roleId');
	const newRole = userRole === '1' ? '0' : '1';

	const result = await fetchWithErrorHandling(relativePath + '/api/users/' + userId + '/role', {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ role: newRole }),
	});

	if (result) {
		popUp('base', 5000, 'Succès', result);
		clearCache();
		await populateUserList();
	}
}

async function handleUserDelete(button) {
	const userId = button.getAttribute('data-userID');

	if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
		return;
	}

	const result = await fetchWithErrorHandling(relativePath + '/api/users/' + userId, { method: 'DELETE', headers: { 'Content-Type': 'application/json' } });

	if (result) {
		popUp('base', 5000, 'Succès', result);
		clearCache();
		await populateUserList();
	}
}

async function handleUserGroupChange(select) {
	const userId = select.getAttribute('data-userID');
	const precedentGroupId = select.getAttribute('data-groupID');
	const groupId = select.value;

	if (precedentGroupId === groupId) return;

	const result = await fetchWithErrorHandling(relativePath + '/api/group/' + groupId + '/users', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ idUser: userId }),
	});

	if (result) {
		popUp('base', 5000, 'Succès', result);
		clearCache();
		await populateUserList();
	}
}

async function populateUserList() {
	const [allUsers, allGroups] = await Promise.all([getAllUsers(), getAllGroups()]);

	if (!allUsers || !allGroups) return;

	const groupsWithNone = [...allGroups, { id: 0, nom: 'Aucun' }];

	const userRows = allUsers
		.map((user) => {
			const groupName = user.groupe_name || 'Aucun';
			const groupOptions = groupsWithNone.map((group) => `<option value="${group.id}" ${user.groupe_id == group.id ? 'selected' : ''}>${group.nom}</option>`).join('');

			return `
			<tr>
				<td>${user.username}</td>
				<td>${user.email}</td>
				<td class="selectGroup">
					<select class="groupChangeSelect" data-userID="${user.id}" data-groupID="${user.groupe_id}" data-groupName="${groupName}">
						${groupOptions}
					</select>
				</td>
				<td class="roleChange">
					<button class="actionButton" data-roleId="${user.role}" data-userID="${user.id}">
						${user.role === '1' ? 'Admin' : 'Utilisateur'}
					</button>
				</td>
				<td class="deleteUser">
					<button class="actionButton" data-userID="${user.id}">Supprimer</button>
				</td>
			</tr>
		`;
		})
		.join('');

	userList.innerHTML = userRows;
}

populateUserList();

const groupList = document.getElementById('groupList');
groupList.addEventListener('click', handleGroupAction);

async function handleGroupAction(e) {
	if (e.target.classList.contains('deleteGroup')) {
		e.preventDefault();
		await handleGroupDelete(e.target);
	}
}

async function handleGroupDelete(button) {
	const groupId = button.getAttribute('data-groupID');

	if (!confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')) {
		return;
	}

	const result = await fetchWithErrorHandling(relativePath + '/api/group/' + groupId, { method: 'DELETE', headers: { 'Content-Type': 'application/json' } });

	if (result) {
		popUp('base', 5000, 'Succès', result);
		clearCache();
		await Promise.all([populateGroupList(), populateUserList()]);
	}
}

async function populateGroupList() {
	const allGroups = await getAllGroups();
	if (!allGroups) return;

	const groupRows = allGroups
		.map(
			(group) => `
		<tr>
			<td>${group.nom}</td>
			<td>
				<button class="actionButton deleteGroup" data-groupID="${group.id}">Supprimer</button>
			</td>
		</tr>
	`
		)
		.join('');

	groupList.innerHTML = groupRows;
}

populateGroupList();

const addGroupBtn = document.getElementById('addGroupBtn');
const groupNameInput = document.getElementById('groupName');

addGroupBtn.addEventListener('click', async (e) => {
	e.preventDefault();
	const groupName = groupNameInput.value.trim();

	if (!groupName) {
		popUp('red', 5000, 'Erreur', 'Veuillez entrer un nom de groupe');
		return;
	}

	const result = await fetchWithErrorHandling(relativePath + '/api/group', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ name: groupName }),
	});

	if (result) {
		popUp('base', 5000, 'Succès', result);
		groupNameInput.value = '';
		clearCache();
		await Promise.all([populateGroupList(), populateUserList()]);
	}
});
