const editAccountBtn = document.getElementById('editAccount');
const changePasswordBtn = document.getElementById('changePassword');

function editAccount() {
	const username = document.querySelector('input[name="username"]');
	const email = document.querySelector('input[name="email"]');
	const idUser = document.querySelector('input[name="idUser"]');

	if (!username.value || !email.value) {
		popUp('red', 5000, 'Erreur', 'Veuillez remplir tous les champs', 'error');
	} else {
		const data = {
			username: username.value,
			email: email.value,
			idUser: idUser.value,
		};
		loadingSpinner();
		fetch(relativePath + '/api/users/' + idUser.value, {
			method: 'PUT',
			body: JSON.stringify(data),
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				removeLoadingSpinner();
				if (data.success) {
					popUp('base', 2000, 'Modification réussi !');
				} else {
					popUp('red', 5000, 'Erreur de modification', data.error);
					form.style.cursor = 'default';
				}
			});
	}
}

editAccountBtn.addEventListener('click', editAccount);

function changePassword() {
	const email = document.querySelector('input[name="email"]');
	if (!email.value) {
		popUp('red', 5000, 'Erreur', 'Le champ email est vide !', 'error');
	}
	const data = {
		email: email.value,
	};
	popUp('base', 2000, 'Veuillez patienter..');
	loadingSpinner();
	fetch(relativePath + '/api/auth/reset-password', {
		method: 'POST',
		body: JSON.stringify(data),
		headers: {
			'Content-Type': 'application/json',
		},
	})
		.then((response) => response.json())
		.then((data) => {
			removeLoadingSpinner();
			if (data.success) {
				popUp('base', 2000, 'Lien de changement envoyé par mail !');
			} else {
				popUp('red', 5000, 'Erreur de changement de mot de passe', data.error);
			}
		});
}

changePasswordBtn.addEventListener('click', changePassword);
