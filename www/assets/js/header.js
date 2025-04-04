const loginBtn = document.querySelector('.CTA_nav#loginBtn');
const registerBtn = document.querySelector('.CTA_nav#registerBtn');
const logoutBtn = document.querySelector('.CTA_nav#logoutBtn');
const relativePath = document.querySelector('#relativePath').textContent;

function createModal(forWhat) {
	const modal = document.createElement('div');
	modal.id = forWhat + '_modal';
	modal.className = 'modalJavascript';
	const container = document.createElement('div');
	container.className = 'containerJavascript';
	let content = null;
	switch (forWhat) {
		case 'login':
			content = loginModal();

			break;
		case 'register':
			content = registerModal();
			break;
		case 'forgotPassword':
			content = forgotPasswordModal();
			break;
		default:
			break;
	}

	const closeBtn = document.createElement('button');
	closeBtn.innerHTML = 'Fermer';
	closeBtn.className = 'CTA_btn';
	closeBtn.addEventListener('click', () => {
		modal.remove();
	});

	const forgotPassword = document.createElement('button');
	forgotPassword.className = 'CTA_btn';
	forgotPassword.textContent = 'Mot de passe oublié ?';
	forgotPassword.addEventListener('click', () => {
		modal.remove();
		createModal('forgotPassword');
	});

	container.append(content);
	if (forWhat === 'login') {
		container.append(forgotPassword);
	}
	container.append(closeBtn);
	modal.append(container);
	document.body.append(modal);
}

function loginModal() {
	const identifiant = document.createElement('input');
	identifiant.type = 'text';
	identifiant.name = 'identifiant';
	identifiant.placeholder = "Nom d'utilisateur ou adresse mail";
	identifiant.required = true;
	const password = document.createElement('input');
	password.type = 'password';
	password.name = 'password';
	password.placeholder = 'Mot de passe';
	password.required = true;
	const submit = document.createElement('input');
	submit.type = 'submit';
	submit.value = 'Se connecter';
	submit.className = 'CTA_btn';

	const form = document.createElement('form');
	form.id = 'loginForm';

	form.append(identifiant, password, submit);

	form.addEventListener('submit', (e) => {
		e.preventDefault();
		const data = new FormData(form);
		form.style.cursor = 'wait';
		popUp('base', 2000, 'Connexion en cours...');
		loadingSpinner();
		fetch(relativePath + 'api/auth/login', {
			method: 'POST',
			body: JSON.stringify(Object.fromEntries(data.entries())),
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				removeLoadingSpinner();
				if (data.success) {
					popUp('base', 2000, 'Connexion réussi !');
					window.location.reload();
				} else {
					popUp('red', 5000, 'Erreur de connexion', data.error);
					form.style.cursor = 'default';
				}
			});
	});

	return form;
}

function registerModal() {
	const username = document.createElement('input');
	username.type = 'text';
	username.name = 'username';
	username.placeholder = "Nom d'utilisateur";
	username.required = true;
	const email = document.createElement('input');
	email.type = 'email';
	email.name = 'email';
	email.placeholder = 'Adresse mail';
	email.required = true;
	const password = document.createElement('input');
	password.type = 'password';
	password.name = 'password';
	password.placeholder = 'Mot de passe';
	password.required = true;
	const groupID = document.createElement('input');
	groupID.type = 'text';
	groupID.name = 'groupID';
	groupID.placeholder = 'Code de groupe (facultatif)';
	groupID.required = false;
	const submit = document.createElement('input');
	submit.type = 'submit';
	submit.value = "S'inscrire";
	submit.className = 'CTA_btn';

	const form = document.createElement('form');
	form.id = 'registerForm';

	form.append(username, email, password, groupID, submit);

	form.addEventListener('submit', (e) => {
		e.preventDefault();
		const data = new FormData(form);
		form.style.cursor = 'wait';
		popUp('base', 2000, 'Inscription en cours...');
		loadingSpinner();
		fetch(relativePath + 'api/auth/register', {
			method: 'POST',
			body: JSON.stringify(Object.fromEntries(data.entries())),
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				removeLoadingSpinner();
				if (data.success) {
					popUp('base', 2000, 'Inscription réussi !');
					window.location.reload();
				} else {
					popUp('red', 5000, "Erreur d'inscription", data.error);
					form.style.cursor = 'default';
				}
			});
	});

	return form;
}

function logout() {
	loadingSpinner();
	fetch(relativePath + 'api/auth/logout', {
		method: 'POST',
	})
		.then((response) => response.json())
		.then((data) => {
			removeLoadingSpinner();
			if (data.success) {
				window.location.reload();
			} else {
				popUp('red', 5000, 'Erreur de déconnexion', data.error);
			}
		});
}

loginBtn?.addEventListener('click', () => {
	createModal('login');
});

registerBtn?.addEventListener('click', () => {
	createModal('register');
});

logoutBtn?.addEventListener('click', () => {
	logout();
});

function changePasswordModal(email, token) {
	const password = document.createElement('input');
	password.type = 'password';
	password.name = 'password';
	password.placeholder = 'Nouveau mot de passe';
	password.required = true;

	const confirmPassword = document.createElement('input');
	confirmPassword.type = 'password';
	confirmPassword.name = 'passwordConfirm';
	confirmPassword.placeholder = 'Confirmer le mot de passe';
	confirmPassword.required = true;

	const submit = document.createElement('input');
	submit.type = 'submit';
	submit.value = 'Changer le mot de passe';
	submit.className = 'CTA_btn';

	const resendBtn = document.createElement('button');
	resendBtn.type = 'button';
	resendBtn.className = 'CTA_btn';
	resendBtn.textContent = 'Renvoyer le mail';

	const form = document.createElement('form');
	form.id = 'changePasswordForm';

	// Email affiché en lecture seule (ou caché)
	const emailInput = document.createElement('input');
	emailInput.type = 'email';
	emailInput.name = 'email';
	emailInput.value = email;
	emailInput.readOnly = true;
	emailInput.style.display = 'none'; // tu peux l'afficher si tu préfères

	// Token caché
	const tokenInput = document.createElement('input');
	tokenInput.type = 'hidden';
	tokenInput.name = 'token';
	tokenInput.value = token;

	form.append(emailInput, tokenInput, password, confirmPassword, submit, resendBtn);

	form.addEventListener('submit', (e) => {
		e.preventDefault();
		const formData = new FormData(form);
		const pass = formData.get('password');
		const confirm = formData.get('passwordConfirm');

		if (pass !== confirm) {
			popUp('red', 4000, 'Les mots de passe ne correspondent pas');
			return;
		}

		form.style.cursor = 'wait';
		popUp('base', 2000, 'Changement en cours...');
		loadingSpinner();
		fetch(relativePath + 'api/auth/change-password', {
			method: 'POST',
			body: JSON.stringify(Object.fromEntries(formData.entries())),
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				removeLoadingSpinner();
				if (data.success) {
					popUp('base', 3000, 'Mot de passe changé avec succès');

					setTimeout(() => {
						window.location.search = '';
						window.location.reload();
					}, 3000);
					form.style.cursor = 'default';
				} else {
					popUp('red', 5000, 'Erreur', data.error);
					form.style.cursor = 'default';
				}
			});
	});

	resendBtn.addEventListener('click', () => {
		popUp('base', 2000, 'Renvoi en cours...');
		loadingSpinner();
		fetch(relativePath + 'api/auth/reset-password', {
			method: 'POST',
			body: JSON.stringify({ email: email }),
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then((res) => res.json())
			.then((data) => {
				removeLoadingSpinner();
				if (data.success) {
					popUp('base', 3000, 'Email renvoyé avec succès !');
				} else {
					popUp('red', 5000, 'Erreur', data.error);
				}
			});
	});

	return form;
}

function forgotPasswordModal() {
	const email = document.createElement('input');
	email.type = 'email';
	email.name = 'email';
	email.placeholder = 'Adresse email';
	email.required = true;

	const submit = document.createElement('input');
	submit.type = 'submit';
	submit.value = 'Envoyer le lien';
	submit.className = 'CTA_btn';

	const form = document.createElement('form');
	form.id = 'forgotPasswordForm';
	form.append(email, submit);

	form.addEventListener('submit', (e) => {
		e.preventDefault();
		const data = new FormData(form);
		form.style.cursor = 'wait';
		popUp('base', 2000, 'Envoi en cours...');
		loadingSpinner();
		fetch(relativePath + 'api/auth/reset-password', {
			method: 'POST',
			body: JSON.stringify(Object.fromEntries(data.entries())),
			headers: {
				'Content-Type': 'application/json',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				removeLoadingSpinner();
				if (data.success) {
					popUp('base', 5000, 'Email envoyé avec succès !');
					form.style.cursor = 'default';
				} else {
					popUp('red', 5000, 'Erreur', data.error);
					form.style.cursor = 'default';
				}
			});
	});

	return form;
}

// Exemple : extraire email/token depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const email = urlParams.get('email');
const token = urlParams.get('token');

if (email && token) {
	const modal = document.createElement('div');
	modal.id = 'changePassword_modal';
	modal.className = 'modalJavascript';

	const container = document.createElement('div');
	container.className = 'containerJavascript';

	const closeBtn = document.createElement('button');
	closeBtn.innerHTML = 'Fermer';
	closeBtn.className = 'CTA_btn';
	closeBtn.addEventListener('click', () => {
		modal.remove();
	});

	container.append(changePasswordModal(email, token));
	container.append(closeBtn);
	modal.append(container);
	document.body.append(modal);
}
