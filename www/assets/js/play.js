const questionsToUse = JSON.parse(window.atob(questionJSON));
const listOfTypes = JSON.parse(window.atob(typesJSON));
const questionnaireID = window.atob(questionnaireIDJSON);
const userID = window.atob(userIDJSON);
const previousScores = JSON.parse(window.atob(previousScoresJSON));

// Sélection de l'élément principal de l'application
const app = document.getElementById('app');
// Récupération des éléments audio
const audio = new Audio('../../assets/sound/quizMusic.mp3');
const correctAnswer = new Audio('../../assets/sound/correctAnswer.mp3');
const wrongAnswer = new Audio('../../assets/sound/wrongAnswer.mp3');

let isMute = false;

function playMusic() {
	audio.play();
	audio.volume = 0.15;
	audio.loop = true;
	console.log('Musique activée');
	if (!document.querySelector('.volume')) {
		const volume = document.createElement('div');
		volume.classList.add('volume');
		volume.innerHTML = `
		<img src="../../assets/img/volume_on.svg" alt="volume">
		`;

		volume.querySelector('img').addEventListener('click', () => {
			if (isMute) {
				playMusic();
				isMute = false;
				volume.querySelector('img').src = '../../assets/img/volume_on.svg';
			} else {
				pauseMusic();
				isMute = true;
				volume.querySelector('img').src = '../../assets/img/volume_off.svg';
			}
		});

		console.log(volume);
		document.querySelector('.gamePage').appendChild(volume);
	}
}

function pauseMusic() {
	console.log('Musique mise sur pause');
	audio.pause();
}

function stopMusic() {
	audio.pause();
	audio.currentTime = 0;

	const volume = document.querySelector('.volume');
	if (volume) {
		volume.remove();
	}
}

function correctAnswerSound() {
	correctAnswer.play();
}

function wrongAnswerSound() {
	wrongAnswer.play();
}

// Fonction pour afficher la page de configuration
function configPage() {
	app.innerHTML = ''; // Réinitialisation du contenu de l'application
	stopMusic();
	const configPage = document.createElement('div'); // Création d'un nouvel élément div
	configPage.classList.add('configPage'); // Ajout d'une classe CSS
	configPage.innerHTML = `
        <h1>Configuration</h1>
        <div class="configContainer">
            <div class="configItem">
                <label>
                Quand voir les résultats
                <select id="results">
                    <option value="instant">Instantané</option>
                    <option value="page">A la fin uniquement</option>
                </select>
                </label>
            </div>
            <div class="configItem">
                <label>
                Temps pour chaque question
                <input type="number" id="time" value="15" min="10" max="60">
                </label>
            </div>
			<div class="configItem">
				<label>
				Utiliser le système de points
				<select id="points">
					<option value="true">Oui</option>
					<option value="false">Non</option>
				</select>
				</label>
        </div>
        <button class="actionButton">Commencer</button>
        <button class="actionButton" onclick="window.location.href = '../'">Retourner au menu</button>
    `;

	app.appendChild(configPage); // Ajout de la page de configuration au DOM

	// Ajout d'un gestionnaire d'événement pour le bouton "Commencer"
	configPage.querySelector('button:first-of-type').addEventListener('click', () => {
		// Récupération des valeurs des champs de configuration
		const results = document.getElementById('results').value;
		const time = document.getElementById('time').value;
		const points = document.getElementById('points').value;
		// Appel de la fonction pour démarrer le jeu
		startGame(results, time, points);
	});
}

function getLibelleType(type) {
	let libelle = '';
	listOfTypes.success.forEach((element) => {
		if (element.id_type === type) {
			libelle = element.nom;
		}
	});
	return libelle;
}

// Fonction principale pour démarrer le jeu
async function startGame(results, time, points) {
	const amountOfQuestions = questionsToUse.success.length;
	console.log('Résultats:', results);
	console.log('Temps:', time);
	console.log('Points:', points);
	console.log('Début du jeu');

	document.querySelector('.configPage').remove(); // Suppression de la page de configuration
	const gamePage = document.createElement('div'); // Création d'un nouvel élément div pour la page du jeu
	gamePage.classList.add('gamePage'); // Ajout d'une classe CSS

	let timerGlobal = parseInt((time * amountOfQuestions) / 1.5);
	let resultShowed = false;
	console.log('Temps global:', timerGlobal);
	const timerGlobalDiv = document.createElement('span');
	timerGlobalDiv.classList.add('timerGlobal');
	timerGlobalDiv.innerHTML = `${timerGlobal}s`;

	gamePage.appendChild(timerGlobalDiv);

	const intervalGlobal = setInterval(() => {
		timerGlobal--;
		timerGlobalDiv.innerHTML = `${timerGlobal}s`;
		if (timerGlobal === 0) {
			clearInterval(intervalGlobal);
			const nombreDeQuestionACompleter = amountOfQuestions - reponseQuestions.length;
			for (let i = 0; i < nombreDeQuestionACompleter; i++) {
				reponseQuestions.push(['Pas de réponse']);
			}
			if (!resultShowed) {
				showResult(questionsMelange, reponseQuestions);
				resultShowed = true;
			}
		}
	}, 1000);

	let questionsMelange = []; // Liste pour stocker les questions mélangées
	console.log('Récupération des questions...');
	try {
		const questions = questionsToUse.success; // Extraction des questions
		console.log('Questions récupérées:', questions);

		let newJSONStructure = {};
		questions.forEach((question, index) => {
			if (getLibelleType(question.type) == 'Multiples') {
				newJSONStructure[index] = {
					index: index,
					question: question.question,
					options: question.choix.split(','),
					answer: question.reponses.split(','),
					points: question.points.split(','),
					type: 'Multiples',
				};
			} else if (getLibelleType(question.type) == 'Vrai/Faux') {
				newJSONStructure[index] = {
					index: index,
					question: question.question,
					options: ['Vrai', 'Faux'],
					points: question.points.split(','),
					answer: [question.reponses],
					type: 'Vrai/Faux',
				};
			}
		});

		// Mélange des questions et sélection d'un sous-ensemble
		questionsMelange = Object.values(newJSONStructure)
			.sort(() => Math.random() - 0.5)
			.slice(0, amountOfQuestions);
		console.log('Questions mélangées:', questionsMelange);

		// Ajout de la page de jeu au DOM
		app.appendChild(gamePage);
	} catch (error) {
		console.error('Erreur:', error); // Gestion des erreurs lors du chargement des questions
	}

	let reponseQuestions = []; // Liste pour stocker les réponses de l'utilisateur

	playMusic(); // Démarrage de la musique

	// Fonction pour créer et afficher une question
	async function createQuestion(question, index) {
		return new Promise((resolve) => {
			console.log('Création de la question n°', index);

			// Mélange des réponses si nécessaire
			let allReponses = [...question.options];
			if (question.type != 'Vrai/Faux') {
				allReponses = allReponses.sort(() => Math.random() - 0.5);
			}

			// Initialisation du temps restant pour répondre
			let tempsRestant = time;
			let repondu = false;

			// Création d'un conteneur pour la question
			const questionContainer = document.createElement('div');
			questionContainer.classList.add('questionContainer');
			questionContainer.innerHTML = `
                <h1 class="question">${question.question}</h1>
                <span class="counter">${index}/${questionsMelange.length}</span>
                <span class="timer">${tempsRestant}s</span>
                <div class="reponses">
                    ${allReponses
						.map((reponse) => {
							return `
                        <label class="radio">
                            <input type="radio" name="radio">
                            <span class="reponse">${reponse}</span>
                        </label>
                        `;
						})
						.join('')}
                </div>
            `;

			// Ajout du bouton suivant ou résultats
			questionContainer.innerHTML += `
                <button class="actionButton">${index === questionsMelange.length ? 'Résultats' : 'Suivant'}</button>
            `;

			questionContainer.querySelector('.reponses').animate(
				[
					{ transform: 'translateX(-100%)', opacity: 0 },
					{ transform: 'translateX(0)', opacity: 1 },
				],
				{
					duration: 1000,
					easing: 'ease-in-out',
				}
			);

			questionContainer.querySelector('.question').animate(
				[
					{ transform: 'translateY(-100%)', opacity: 0 },
					{ transform: 'translateY(0)', opacity: 1 },
				],
				{
					duration: 1000,
					easing: 'ease-in-out',
				}
			);

			// Gestionnaire d'événement pour le bouton "Suivant" ou "Résultats"
			questionContainer.querySelector('.actionButton').addEventListener('click', () => {
				// Récupération de la réponse sélectionnée par l'utilisateur
				const reponses = questionContainer.querySelectorAll(`.radio`);
				let reponseToPush = [];
				reponses.forEach((reponse) => {
					if (reponse.querySelector('input').checked) {
						reponseToPush.push(reponse.querySelector('.reponse').textContent);
					}
				});
				if (reponseToPush.length == 0) {
					reponseToPush.push('Pas de réponse');
				}
				console.log('Réponse sélectionnée:', reponseToPush);
				console.log('Réponse correcte:', question.answer);

				// Si une réponse est sélectionnée ou si le temps est écoulé
				if (reponseToPush || tempsRestant == 0) {
					if (!repondu) {
						console.log('Okay');
						reponseQuestions.push(reponseToPush != ['Pas de réponse'] ? reponseToPush : ['Pas de réponse']); // Ajout de la réponse à la liste
						if (results == 'instant') {
							clearInterval(interval);
							let divReponses = ``;
							reponseToPush.forEach((reponseToPush) => {
								for (i = 0; i < question.answer.length; i++) {
									if (reponseToPush == question.answer[i]) {
										correctAnswerSound();
									} else {
										wrongAnswerSound();
									}
									divReponses += `
								<div class="reponses background_${reponseToPush == question.answer[i]}">
									<div class="reponse">Votre réponse: ${reponseToPush}</div>
									<div class="reponse">Réponse correcte: ${question.answer[i]}</div>
								</div>`;
								}
							});

							questionContainer.innerHTML = `
                        <h1 class="question">${question.question}</h1>
                        <span class="counter">${index}/${questionsMelange.length}</span>
                        ${divReponses}
                        <div>Passage à la question suivante dans 2 secondes...</div>`;
							setTimeout(() => {
								questionContainer.remove(); // Suppression de la question affichée
								resolve(); // Passage à la question suivante
							}, 2000);
						} else {
							clearInterval(interval);
							questionContainer.querySelector('.reponses').classList.add('exitAnimation');
							questionContainer.querySelector('.question').classList.add('exitAnimation');
							setTimeout(() => {
								questionContainer.remove(); // Suppression de la question affichée
								resolve(); // Passage à la question suivante
							}, 1000);
						}
					}
				} else {
					alert('Vous devez choisir une réponse'); // Alerte si aucune réponse n'est sélectionnée
				}
			});

			gamePage.appendChild(questionContainer); // Ajout de la question à la page

			// Gestion du chronomètre
			const timer = document.querySelector('.timer');
			const interval = setInterval(() => {
				if (timerGlobal > 0) {
					tempsRestant--; // Décrémentation du temps restant
					timer.innerHTML = tempsRestant + 's'; // Mise à jour de l'affichage du temps
					if (tempsRestant === 0) {
						clearInterval(interval); // Arrêt du chronomètre
						console.log('Temps écoulé');
						if (questionContainer.querySelector('.actionButton')) {
							questionContainer.querySelector('.actionButton').click(); // Passage automatique à la question suivante
						}
					}
				} else {
					clearInterval(interval);
				}
			}, 1000);
		});
	}
	// Création et affichage des questions
	for (const [index, question] of questionsMelange.entries()) {
		if (timerGlobal > 0) {
			await createQuestion(question, index + 1);
		}
	}

	console.log('Fin du jeu');
	if (timerGlobal > 0) {
		if (!resultShowed) {
			showResult(questionsMelange, reponseQuestions); // Affichage des résultats
			resultShowed = true;
		}
	}

	// Fonction pour afficher les résultats
	function showResult(questions, reponses) {
		gamePage.innerHTML = ''; // Réinitialisation de la page
		const resultPage = document.createElement('div'); // Création d'un conteneur pour les résultats
		resultPage.classList.add('resultPage');

		let score = 0; // Initialisation du score
		let maxPoints = 0; // Initialisation du nombre de points maximum
		console.log('Réponses Object', reponses);
		questions.forEach((question, index) => {
			if (points == 'true') {
				console.log('Question:', question);
				for (i = 0; i < question.answer.length; i++) {
					let pointPourLaQuestion = 0;
					if (question.answer[i] != 'Pas de réponse') {
						pointPourLaQuestion = parseInt(question.points[question.options.indexOf(question.answer[i])]);
					}
					console.log('Point pour la question:', pointPourLaQuestion);
					reponses[index].forEach((reponse) => {
						if (question.answer[i] == reponse) {
							score += pointPourLaQuestion; // Incrémentation du score si la réponse est correcte
						}
						maxPoints += pointPourLaQuestion;
					});
				}
			} else {
				for (i = 0; i < question.answer.length; i++) {
					reponses[index].forEach((reponse) => {
						if (question.answer[i] == reponse) {
							score++; // Incrémentation du score si la réponse est correcte
						}
						maxPoints++;
					});
				}
			}
		});
		if (score < 0) {
			score = 0;
		}
		if (maxPoints < 0) {
			maxPoints = 0;
		}
		console.log(previousScores);
		console.log(questionnaireID);
		console.log(userID);
		console.log('Score:', score);
		console.log('Nombre de points maximum:', maxPoints);
		const [bestScore, bestScoreOn] = getBestScore(score, maxPoints);
		registerScore(score, maxPoints, reponses);

		// Affichage des résultats
		resultPage.innerHTML = `
            <h1>Résultat</h1>
            ${score > bestScore ? `<h2>Nouveau meilleur score: ${score}/${maxPoints}</h2>` : `<h2>Meilleur score: ${bestScore}/${bestScoreOn}</h2>`}
            <div class="score">Score: ${score}/${maxPoints}</div>
            ${questions
				.map((question, index) => {
					let divReponses = ``;
					question.answer.forEach((answer, i) => {
						reponses[index].forEach((reponse) => {
							divReponses += `
							<div class="reponses background_${reponse == answer}">
								<div class="reponse">Points: ${question.points[question.options.indexOf(answer)]}</div>
								<div class="reponse">Votre réponse: ${reponse}</div>
								<div class="reponse">Réponse correcte: ${answer}</div>
							</div>`;
						});
					});
					return `
                    <div class="resultContainer">
                        <div class="question">${index + 1} - ${question.question}</div>
						${divReponses}
                    </div>
                `;
				})
				.join('')}
            <button class="actionButton">Recommencer</button>
        `;
		gamePage.appendChild(resultPage);

		// Gestionnaire d'événement pour le bouton "Recommencer"
		resultPage.querySelector('.actionButton').addEventListener('click', () => {
			gamePage.remove(); // Suppression de la page des résultats
			configPage(); // Retour à la page de configuration
		});
	}

	function registerScore(score, maxPoints, reponses) {
		let reponsesList = [];
		questionsMelange.forEach((question, index) => {
			reponsesList.push(question.index + '_' + reponses[index]);
		});

		reponsesList.sort((a, b) => {
			return a.split('_')[0] - b.split('_')[0];
		});

		const data = {
			score: score,
			score_on: maxPoints,
			id_user: userID,
			reponses: reponsesList.join(','),
		};

		fetch(`../../api/questionnaires/${questionnaireID}/score`, {
			method: 'POST',
			body: JSON.stringify(data),
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				console.log('Score enregistré:', data);
			})
			.catch((error) => {
				console.error('Erreur:', error);
			});
	}

	function getBestScore(score, maxPoints) {
		if (previousScores.success) {
			const previousScoresToUse = previousScores.success;
			previousScoresToUse.forEach((element) => {
				if (element.score / element.score_on > score / maxPoints) {
					return [element.score, element.score_on];
				}
			});
			return [score, maxPoints];
		}
	}
}

configPage();
