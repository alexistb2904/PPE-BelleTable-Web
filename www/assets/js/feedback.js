document.getElementById('feedbackForm').addEventListener('submit', function (e) {
	e.preventDefault();
	loadingSpinner();
	const rating = document.querySelector('input[name="rating"]:checked');
	const comment = document.getElementById('comment').value;

	if (!rating) {
		document.getElementById('feedbackMessage').innerText = 'Veuillez sélectionner une note.';
		removeLoadingSpinner();
		return;
	}

	if (comment.length > 500) {
		document.getElementById('feedbackMessage').innerText = 'Le commentaire ne doit pas dépasser 500 caractères.';
		removeLoadingSpinner();
		return;
	}

	const questionnaireID = new URLSearchParams(window.location.search).get('id');
	if (!questionnaireID) {
		document.getElementById('feedbackMessage').innerText = 'ID de questionnaire manquant.';
		removeLoadingSpinner();
		return;
	}
	fetch('../../api/questionnaires/' + questionnaireID + '/feedback', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			rating: rating.value,
			comment: comment,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				document.getElementById('feedbackMessage').innerText = 'Merci pour votre avis !';
			} else {
				document.getElementById('feedbackMessage').innerText = data.error || 'Une erreur est survenue. Veuillez réessayer.';
			}
			removeLoadingSpinner();
		})
		.catch((error) => {
			console.error('Erreur:', error);
			document.getElementById('feedbackMessage').innerText = 'Une erreur est survenue. Veuillez réessayer.';
			removeLoadingSpinner();
		});
});
