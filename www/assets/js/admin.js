const chartAnwser = document.getElementById('chartAnwser');
const quizSelect = document.getElementById('quizSelect');
let chartPrincipal = null;
let allScoreFromAllResult = null;
async function generateChart() {
	loadingSpinner();
	const allScoreFromAll = await fetch(relativePath + '/api/questionnaires/scores')
		.then((response) => response.json())
		.then((data) => {
			removeLoadingSpinner();
			if (data.success) {
				return data.success;
			} else {
				popUp('red', 5000, 'Erreur', data.error);
				return null;
			}
		});

	allScoreFromAllResult = allScoreFromAll;
	// With apexcharts
	// Data is in format score, score_on, date, username, reponses, groupe_name, user_id, reponses, id_questionnaire
	// Graph for the number of responses by questionnaire on the last 7 days

	// Get the last 7 days
	let last7Days = [];
	for (let i = 6; i >= 0; i--) {
		const date = new Date();
		date.setDate(date.getDate() - i);
		last7Days.push(date.toISOString().split('T')[0]);
	}

	// Get the number of answers by day
	let numberOfAnswersByDay = [];
	for (let i = 0; i < 7; i++) {
		let count = 0;
		for (let j = 0; j < allScoreFromAll.length; j++) {
			if (new Date(allScoreFromAll[j].date.split(' ')[0]).toLocaleDateString() == new Date(last7Days[i]).toLocaleDateString()) {
				count++;
			}
		}
		numberOfAnswersByDay.push(count);
	}
	console.log(numberOfAnswersByDay);
	const options = {
		chart: {
			type: 'line',
		},
		series: [
			{
				name: 'Réponses',
				data: numberOfAnswersByDay,
			},
		],
		xaxis: {
			categories: last7Days.map((date) => new Date(date).toLocaleDateString()),
		},
	};

	quizSelect.innerHTML = '';
	// Prendre une liste de tous les questionnaires sans doublons
	const allQuestionnaire = allScoreFromAll.map((score) => score.questionnaire_name).filter((value, index, self) => self.indexOf(value) === index);
	quizSelect.innerHTML += `<option value="all">Tous les questionnaires</option>`;
	allQuestionnaire.forEach((questionnaire) => {
		quizSelect.innerHTML += `<option value="${questionnaire}">${questionnaire}</option>`;
	});

	chartPrincipal = new ApexCharts(chartAnwser, options);
	chartPrincipal.render();

	quizSelect.addEventListener('change', (e) => {
		const selectedQuiz = e.target.value;
		const quizName = e.target.options[e.target.selectedIndex].text;
		document.getElementById('quizName').textContent = quizName;
		if (selectedQuiz == 'all') {
			// Get the number of answers by day
			let numberOfAnswersByDay = [];
			for (let i = 0; i < 7; i++) {
				let count = 0;
				for (let j = 0; j < allScoreFromAll.length; j++) {
					if (new Date(allScoreFromAll[j].date.split(' ')[0]).toLocaleDateString() == new Date(last7Days[i]).toLocaleDateString()) {
						count++;
					}
				}
				numberOfAnswersByDay.push(count);
			}
			options.series[0].data = numberOfAnswersByDay;
			options.xaxis.categories = last7Days.map((date) => new Date(date).toLocaleDateString());
		} else {
			// Get the number of answers by day
			let numberOfAnswersByDay = [];
			for (let i = 0; i < 7; i++) {
				let count = 0;
				for (let j = 0; j < allScoreFromAll.length; j++) {
					if (
						new Date(allScoreFromAll[j].date.split(' ')[0]).toLocaleDateString() == new Date(last7Days[i]).toLocaleDateString() &&
						allScoreFromAll[j].questionnaire_name == selectedQuiz
					) {
						count++;
					}
				}
				numberOfAnswersByDay.push(count);
			}
			options.series[0].data = numberOfAnswersByDay;
			options.xaxis.categories = last7Days.map((date) => new Date(date).toLocaleDateString());
		}
		chartPrincipal.updateOptions(options);
	});
}

generateChart();

const userList = document.getElementById('userList');

async function getAllUsers() {
	loadingSpinner();
	const allUsers = await fetch(relativePath + '/api/users')
		.then((response) => response.json())
		.then((data) => {
			removeLoadingSpinner();
			if (data.success) {
				return data.success;
			} else {
				popUp('red', 5000, 'Erreur', data.error);
				return null;
			}
		});
	return allUsers;
}

async function getAllGroups() {
	loadingSpinner();
	const allGroups = await fetch(relativePath + '/api/group')
		.then((response) => response.json())
		.then((data) => {
			removeLoadingSpinner();
			if (data.success) {
				return data.success;
			} else {
				popUp('red', 5000, 'Erreur', data.error);
				return null;
			}
		});
	return allGroups;
}

async function populateUserList() {
	const allUsers = await getAllUsers();
	const allGroups = await getAllGroups();
	allGroups.push({ id: 0, nom: 'Aucun' });
	userList.innerHTML = '';
	if (allUsers) {
		allUsers.forEach(async (user) => {
			user.groupe_name = user.groupe_name == null ? 'Aucun' : user.groupe_name;
			const userGroup = document.createElement('select');
			userGroup.classList.add('groupChangeSelect');
			userGroup.setAttribute('data-userID', user.id);
			userGroup.setAttribute('data-groupID', user.groupe_id);
			userGroup.setAttribute('data-groupName', user.groupe_name);

			allGroups.forEach((group) => {
				const option = document.createElement('option');
				option.value = group.id;
				option.textContent = group.nom;
				if (user.groupe_id == group.id) {
					option.selected = true;
				}
				userGroup.appendChild(option);
			});
			const tr = document.createElement('tr');
			tr.innerHTML = `
			<tr>
				<td>${user.username}</td>
				<td>${user.email}</td>
				<td class="selectGroup"></td>
				<td class="roleChange" ><button class="actionButton" data-roleId="${user.role}" data-userID="${user.id}">${user.role == '1' ? 'Admin' : 'Utilisateur'}</button></td>
				<td class="deleteUser"><button class="actionButton" data-userID="${user.id}">Supprimer</button></td>
			</tr>`;

			tr.querySelector('.selectGroup').appendChild(userGroup);
			userList.appendChild(tr);

			const roleChange = tr.querySelector('.roleChange button');
			roleChange.addEventListener('click', async (e) => {
				const userId = e.target.getAttribute('data-userID');
				const userRole = e.target.getAttribute('data-roleId');
				const newRole = userRole == '1' ? '0' : '1';
				const result = await fetch(relativePath + '/api/users/' + userId + '/role', {
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						role: newRole,
					}),
				})
					.then((response) => response.json())
					.then(async (data) => {
						if (data.success) {
							popUp('base', 5000, 'Succès', data.success);
							await populateUserList();
						} else {
							popUp('red', 5000, 'Erreur', data.error);
						}
					});
			});
			const groupChange = tr.querySelector('.groupChangeSelect');
			groupChange.addEventListener('change', async (e) => {
				const userId = e.target.getAttribute('data-userID');
				const precedentGroupId = e.target.getAttribute('data-groupID');
				const groupId = e.target.value;
				if (precedentGroupId == groupId) {
					return;
				}

				const result = await fetch(relativePath + '/api/group/' + groupId + '/users', {
					method: 'post',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						idUser: userId,
					}),
				})
					.then((response) => response.json())
					.then(async (data) => {
						if (data.success) {
							popUp('base', 5000, 'Succès', data.success);
							await populateUserList();
						} else {
							popUp('red', 5000, 'Erreur', data.error);
						}
					});
			});

			const deleteUser = tr.querySelector('.deleteUser button');
			deleteUser.addEventListener('click', async (e) => {
				const userId = e.target.getAttribute('data-userID');
				const result = await fetch(relativePath + '/api/users/' + userId, {
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				})
					.then((response) => response.json())
					.then(async (data) => {
						if (data.success) {
							popUp('base', 5000, 'Succès', data.success);
							await populateUserList();
						} else {
							popUp('red', 5000, 'Erreur', data.error);
						}
					});
			});
		});
	}
}

populateUserList();

const groupList = document.getElementById('groupList');

async function populateGroupList() {
	const allGroups = await getAllGroups();
	groupList.innerHTML = '';
	if (allGroups) {
		allGroups.forEach(async (group) => {
			const tr = document.createElement('tr');
			tr.innerHTML = `
			<tr>
				<td>${group.nom}</td>
				<td><button class="actionButton deleteGroup" data-groupID="${group.id}">Supprimer</button></td>
			</tr>`;
			groupList.appendChild(tr);

			const deleteGroup = tr.querySelector('.deleteGroup');
			deleteGroup.addEventListener('click', async (e) => {
				const groupId = e.target.getAttribute('data-groupID');
				const result = await fetch(relativePath + '/api/group/' + groupId, {
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				})
					.then((response) => response.json())
					.then(async (data) => {
						if (data.success) {
							popUp('base', 5000, 'Succès', data.success);
							await populateGroupList();
							await populateUserList();
						} else {
							popUp('red', 5000, 'Erreur', data.error);
						}
					});
			});
		});
	}
}

populateGroupList();

const addGroupBtn = document.getElementById('addGroupBtn');
const groupNameInput = document.getElementById('groupName');

addGroupBtn.addEventListener('click', async (e) => {
	const groupName = groupNameInput.value;
	if (groupName == '') {
		popUp('red', 5000, 'Erreur', 'Veuillez entrer un nom de groupe');
		return;
	}
	const result = await fetch(relativePath + '/api/group', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			name: groupName,
		}),
	})
		.then((response) => response.json())
		.then(async (data) => {
			if (data.success) {
				popUp('base', 5000, 'Succès', data.success);
				await populateGroupList();
				await populateUserList();
			} else {
				popUp('red', 5000, 'Erreur', data.error);
			}
		});
});
