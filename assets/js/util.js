/**
 * Affiche une fenêtre contextuelle avec un message personnalisé.
 * @param {string} type - Le type de la fenêtre contextuelle : base,red(par défaut: 'base').
 * @param {number} time - La durée d'affichage de la fenêtre contextuelle en millisecondes (par défaut: 8000).
 * @param {string} titre - Le titre de la fenêtre contextuelle.
 * @param {string} message - Le message de la fenêtre contextuelle.
 * @param {string} google_icon - L'icône Google Font à afficher dans la fenêtre contextuelle.
 */
function popUp(type = 'base' || 'red', time = 8000, titre = '', message = '', google_icon = '', copy = false) {
	const google_icon_svg = `<span class="material-symbols-rounded">${google_icon}</span>`;
	const errorContainer = document.createElement('div');
	errorContainer.classList.add('popup-container');

	const errorNotifContainer = document.createElement('div');
	errorNotifContainer.classList.add(`${type}-notif`);
	if (typeof message === 'object') {
		message = JSON.stringify(message, null, 2);
	}

	if (type == 'red') {
		console.error(titre + ' : ' + message);
	}

	const ContentContainer = document.createElement('div');

	if (copy == true && type == 'red') {
		ContentContainer.innerHTML = `<span id="title-popup">${google_icon_svg}${titre}</span><br><span id="content-popup">${message}<br><md-outlined-button class="width-100">Copier l'erreur</md-outlined-button></span>`;

		ContentContainer.querySelector('md-outlined-button').addEventListener('click', () => {
			if (navigator.clipboard) {
				registerLog("Copie du message d'erreur", "Copie du message d'erreur : " + message);
				navigator.clipboard.writeText(message).then(
					function () {
						console.log('Texte copié dans le presse-papiers : ' + message);
						alert('Erreur Copié dans le presse-papiers, pour résoudre ce problème veuillez contacter Alexis Thierry-Bellefond ( alexis.thierry-bellefond@paris.fr )');
					},
					function (err) {
						console.error('Erreur lors de la copie dans le presse-papiers', err);
					}
				);
			} else {
				console.error("L'API du presse-papiers n'est pas supportée par ce navigateur.");
			}
		});
	} else {
		ContentContainer.innerHTML = `<span id="title-popup">${google_icon_svg}${titre}</span><br><span id="content-popup">${message}</span>`;
	}

	errorNotifContainer.appendChild(ContentContainer);
	errorContainer.appendChild(errorNotifContainer);
	document.body.appendChild(errorContainer);
	let numberOfNotificationAleardyDisplayed = document.querySelectorAll('.popup-container').length;
	numberOfNotificationAleardyDisplayed > 5 ? (numberOfNotificationAleardyDisplayed = 0) : numberOfNotificationAleardyDisplayed;
	errorContainer.animate(
		[
			{
				opacity: '0.2',
				bottom: '-30%',
			},
			{
				opacity: '1',
				bottom: '1' + numberOfNotificationAleardyDisplayed + 'vh',
			},
		],
		{
			duration: 500,
			fill: 'forwards',
		}
	);

	setTimeout(() => {
		errorContainer.animate(
			[
				{
					opacity: '1',
					bottom: '2vh',
				},
				{
					opacity: '0.2',
					bottom: '-30%',
				},
			],
			{
				duration: 500,
				fill: 'forwards',
			}
		);
		setTimeout(() => {
			errorContainer.remove();
		}, 500);
	}, time);
}

function loadingSpinner() {
	const spinnerContainer = document.createElement('div');
	spinnerContainer.classList.add('spinner-container');
	const spinner = document.createElement('div');
	spinner.classList.add('spinner');
	spinner.innerHTML = `<div></div>
	<div></div>
	<div></div>
	<div></div>
	<div></div>
	<div></div>
	`;
	spinnerContainer.appendChild(spinner);
	document.body.appendChild(spinnerContainer);
}

function removeLoadingSpinner() {
	const spinner = document.querySelector('.spinner-container');
	spinner.remove();
}
