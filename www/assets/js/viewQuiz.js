/* Forme HTML du questionnaire */
/*
<div class="questionnaire" data-id="{idQuestionnaire}" data-theme="{theme}">
    <div>
        <h3>${nomQuestionnaire}</h3>
        <p>${theme}</p>
    </div>
    <p><span>{nbQuestions}</span> questions</p>
    <a href="<?php echo $relative_path ?>quiz/view/?id={idQuestionnaire}">Jouer</a>
</div>
*/

const themeSelect = document.querySelector('#theme-select');

themeSelect.addEventListener('change', (e) => {
	const theme = themeSelect.value;
	const questionnaires = document.querySelectorAll('.questionnaire');
	questionnaires.forEach((questionnaire) => {
		if (theme == 'all') {
			questionnaire.style.display = 'flex';
		} else {
			if (questionnaire.dataset.theme == theme) {
				questionnaire.style.display = 'flex';
			} else {
				questionnaire.style.display = 'none';
			}
		}
	});
});

const listeQuestionnaires = document.querySelector('.listeQuestionnaire');

function getQuestionnaires() {
	listeQuestionnaires.innerHTML = '';
	loadingSpinner();
	fetch(relativePath + '/api/questionnaires')
		.then((response) => response.json())
		.then((data) => {
			removeLoadingSpinner();
			if (data.success) {
				data = data.success;
				data.forEach((quiz) => {
					const questionnaire = document.createElement('div');
					questionnaire.className = 'questionnaire';
					questionnaire.dataset.id = quiz.id;
					questionnaire.dataset.theme = quiz.theme;
					questionnaire.innerHTML = `
                    <div>
                        <h3>${quiz.nom}</h3>
                        <p>${quiz.theme}</p>
                    </div>
                    <p><span>${quiz.nb_questions}</span> questions</p>
                    <div>
                    <a class="CTA_btn" id="seeQuestionnaire">Voir</a>
                    <a class="CTA_btn" id="seeAnswers">Voir scores</a>
                    </div>
                    
                `;
					if (!themeSelect.innerHTML.includes(quiz.theme)) {
						const option = document.createElement('option');
						option.value = quiz.theme;
						option.innerHTML = quiz.theme;
						themeSelect.appendChild(option);
					}

					const seeAnswersBtn = questionnaire.querySelector('#seeAnswers');
					seeAnswersBtn.onclick = () => seeAnswers(quiz.id);

					const seeQuestionnaireBtn = questionnaire.querySelector('#seeQuestionnaire');
					seeQuestionnaireBtn.onclick = () => seeQuestionnaire(quiz.id, quiz.nom);
					listeQuestionnaires.appendChild(questionnaire);
				});
			} else {
				popUp('red', 5000, 'Erreur', data.error);
			}
		});
}

getQuestionnaires();

async function seeAnswers(idQuestionnaire) {
	loadingSpinner();
	const userInfo = await fetch(relativePath + '/api/users/me')
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
	if (userInfo) {
		const urlToFetch = userInfo.role == '1' ? '/score' : '/score/me';
		loadingSpinner();
		const answers = await fetch(relativePath + '/api/questionnaires/' + idQuestionnaire + urlToFetch)
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
		console.log(answers);
		if (answers) {
			const modal = document.createElement('div');
			modal.id = 'answers_modal';
			modal.className = 'modalJavascript';
			const container = document.createElement('div');
			container.className = 'containerJavascript';
			container.innerHTML = `
				<h2>Scores du questionnaire</h2>
				
				`;

			if (answers.length == 0) {
				container.innerHTML += `
				Vous n'avez pas participer à ce quiz.`;
			} else {
				if (userInfo.role == '1') {
					container.innerHTML += `
				<div id="select_div">
					<select id="answers_select">
						<option value="all">Tous</option>
					</select>
				<span>Moyenne du Groupe : <span id="groupe_moy"></span></span></div>`;
				}
			}
			const closeBtn = document.createElement('button');
			closeBtn.innerHTML = 'Fermer';
			closeBtn.className = 'CTA_btn';
			closeBtn.addEventListener('click', () => {
				modal.remove();
			});

			container.append(closeBtn);
			answers.forEach((answer, index) => {
				const answerDiv = document.createElement('div');
				answerDiv.className = 'questionDiv';
				answerDiv.id = 'answer_' + index;
				answerDiv.dataset.id = answer.user_id;
				answerDiv.dataset.groupe_name = answer.groupe_name;
				answerDiv.innerHTML = `
					<div>
					<h3>${answer.username} | ${answer.groupe_name}</h3>
					<h3>${answer.score} / ${answer.score_on}</h3>
					</div>
					`;

				const seeAnswersBtn = document.createElement('a');
				seeAnswersBtn.innerHTML = 'Voir réponses';
				seeAnswersBtn.className = 'CTA_btn';
				seeAnswersBtn.onclick = () => {
					seeAnswersUser(idQuestionnaire, answer.reponses);
				};
				answerDiv.append(seeAnswersBtn);
				container.append(answerDiv);
			});
			if (userInfo.role == '1') {
				const select = container.querySelector('#answers_select');
				const groupeMoy = container.querySelector('#groupe_moy');
				const allAnswers = container.querySelectorAll('.questionDiv');
				const groupes = [];
				answers.forEach((answer) => {
					if (!groupes.includes(answer.groupe_name)) {
						groupes.push(answer.groupe_name);
						const option = document.createElement('option');
						option.value = answer.groupe_name;
						option.innerHTML = answer.groupe_name;
						select.appendChild(option);
					}
				});
				if (answers.length > 0) {
					groupeMoy.innerHTML = calculMoyenne();

					select.addEventListener('change', (e) => {
						groupeMoy.innerHTML = calculMoyenne(select.value);
						allAnswers.forEach((answer) => {
							if (select.value == 'all') {
								answer.style.display = 'flex';
							} else {
								if (answer.dataset.groupe_name == select.value) {
									answer.style.display = 'flex';
								} else {
									answer.style.display = 'none';
								}
							}
						});
					});
				}

				function calculMoyenne(groupe = 'all') {
					let moyenne = 0;
					let count = 0;
					answers.forEach((answer) => {
						if (groupe == 'all') {
							moyenne += answer.score;
							count++;
						} else {
							if (answer.groupe_name == groupe) {
								moyenne += answer.score;
								count++;
							}
						}
					});
					return moyenne / count;
				}
			}

			modal.append(container);
			document.body.append(modal);
		}
	} else {
		popUp('red', 5000, 'Erreur', 'Vous devez être connecté pour voir les réponses');
	}
}

async function seeQuestionnaire(idQuestionnaire, nomQuestionnaire) {
	loadingSpinner();
	let questionnaire = await fetch(relativePath + '/api/questionnaires/' + idQuestionnaire)
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
	if (questionnaire.questions) {
		const modal = document.createElement('div');
		modal.id = 'questionnaire_modal';
		modal.className = 'modalJavascript';
		const container = document.createElement('div');
		container.className = 'containerJavascript';
		container.innerHTML = `
        <h2>${nomQuestionnaire}</h2>
        `;

		const closeBtn = document.createElement('button');
		closeBtn.innerHTML = 'Fermer';
		closeBtn.className = 'CTA_btn';
		closeBtn.addEventListener('click', () => {
			modal.remove();
		});

		const playBtn = document.createElement('a');
		playBtn.innerHTML = 'Jouer';
		playBtn.className = 'CTA_btn';
		playBtn.href = relativePath + '/quiz/play/?id=' + idQuestionnaire;

		container.append(playBtn);
		container.append(closeBtn);
		questionnaire.questions.forEach((question, index) => {
			const questionDiv = document.createElement('div');
			questionDiv.className = 'questionDiv';
			console.log(question);
			questionDiv.id = 'question_' + question.id_question;
			questionDiv.innerHTML = `
            <h3>${question.question}</h3>
            <h3>Type : ${question.type}</h3>
			<h3>Points : ${question.choix.reduce((acc, choix) => acc + choix.points, 0)}</h3>
            `;
			container.append(questionDiv);
		});

		modal.append(container);
		document.body.append(modal);
	}
}

async function seeAnswersUser(idQuestionnaire, reponsesUser) {
	loadingSpinner();
	const questionnaire = await fetch(relativePath + '/api/questionnaires/' + idQuestionnaire)
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
	const modal = document.createElement('div');
	modal.id = 'personal_answers_modal';
	modal.className = 'modalJavascript';
	const container = document.createElement('div');
	container.className = 'containerJavascript';
	container.innerHTML = `
	<h2>Réponses</h2>
	`;
	console.log(reponsesUser);
	console.log(questionnaire);
	/* Structure questionnaire.questions
	[
    {
        "id_question": 2,
        "question": "Napoléon est mort en 1821.",
        "type": "Vrai/Faux",
        "id_creator": 1,
        "choix": [
            {
                "id": 3,
                "texte": "Vrai",
                "est_reponse": 1,
                "points": 1
            },
            {
                "id": 4,
                "texte": "Faux",
                "est_reponse": 0,
                "points": 1
            }
        ]
    },
	]
	*/
	questionnaire.questions.forEach((question, index) => {
		const questionDiv = document.createElement('div');
		questionDiv.className = 'modalQuestionDiv';

		const questionTitle = document.createElement('h3');
		const maxPointsPossible = question.choix.reduce((acc, choix) => acc + choix.points, 0);
		questionTitle.innerText = 'Question ' + (index + 1) + ' (' + maxPointsPossible + ' points) : ' + question.question;
		questionDiv.appendChild(questionTitle);

		// Réponses utilisateur pour cette question
		const userReponses = reponsesUser.filter((reponse) => reponse.id_question == question.id_question);

		const reponseUser = document.createElement('p');
		reponseUser.innerText = 'Réponse Utilisateur : ' + userReponses.map((rep) => rep.choix).join(', ');

		const bonneReponses = question.choix
			.filter((choix) => choix.est_reponse == 1)
			.map((choix) => choix.texte)
			.join(', ');

		// Toutes les réponses utilisateur sont correctes ?
		const toutesBonnes = userReponses.every((rep) => question.choix.find((choix) => choix.id == rep.id_choix && choix.est_reponse == 1));

		if (toutesBonnes) {
			reponseUser.style.color = 'green';
		} else {
			reponseUser.style.color = 'red';
			const reponse = document.createElement('p');
			reponse.innerText = 'Bonne réponse : ' + bonneReponses;
			questionDiv.appendChild(reponse);
		}

		questionDiv.appendChild(reponseUser);
		container.appendChild(questionDiv);
	});

	const closeBtn = document.createElement('button');
	closeBtn.innerHTML = 'Fermer';
	closeBtn.className = 'CTA_btn';
	closeBtn.addEventListener('click', () => {
		modal.remove();
	});

	container.append(closeBtn);
	modal.append(container);

	document.body.append(modal);
}
