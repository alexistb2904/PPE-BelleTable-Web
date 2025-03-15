const chartAnwser = document.getElementById('chartAnwser');

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

	const chart = new ApexCharts(chartAnwser, options);
	chart.render();
}

generateChart();
