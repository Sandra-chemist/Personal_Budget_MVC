const categoryField = document.querySelector('select[name="category"]');
const monthlyLimit = document.querySelector('.monthlyLimit');
const sumExpenses = document.querySelector('.sumExpenses');
const info = document.querySelector('.info');

const date = new Date();
const currentMonth = date.getMonth() + 1;
const nextMonth = date.getMonth() + 2;
const currentYear = date.getFullYear();

const currentMonthStart = currentYear + '-' + currentMonth + '-' + '01';
//console.log(currentMonthStart);

if (currentMonth === 12) {
	year = currentYear + 1;
} else {
	year = currentYear;
}
const nextMonthStart = year + '-' + nextMonth + '-' + '01';
//console.log(nextMonthStart);

let sum = 0;
let limit;

categoryField.addEventListener('change', function () {
	if (categoryField.value) {
		const category = categoryField.options[categoryField.selectedIndex].value;
		//console.log(category);

		fetch('/expense/limit')
			.then(res => res.json())
			.then(data => {
				monthlyLimit.textContent = '';
				sumExpenses.textContent = '';
				info.textContent = '';

				for (let i = 0; i < data.length; i++) {
					limit = parseFloat(data[i].monthly_limit);
					console.log(`limit to: ${limit}`);

					if (data[i].name === category && limit > 0) {
						monthlyLimit.classList.remove('hidden');
						sumExpenses.classList.remove('hidden');
						info.classList.remove('hidden');
						console.log(`kategoria wybrana to ${data[i].name}`);

						monthlyLimit.textContent = `Ustawiony miesięczny limit: ${limit} zł`;
						let monthLimit = parseFloat(limit);
						console.log(monthLimit);
						fetch('/expense/sumMonthlyExpenses')
							.then(res => res.json())
							.then(expenses => {
								for (let j = 0; j < expenses.length; j++) {
									if (
										expenses[j].name === category &&
										expenses[j].date_of_expense >= currentMonthStart &&
										expenses[j].date_of_expense < nextMonthStart
									) {
										console.log(
											`${expenses[j].name} ${expenses[j].date_of_expense} ${expenses[j].amount}`
										);
										sum = sum + parseFloat(expenses[j].amount);
										console.log(sum);
									}
								}

								sumExpenses.textContent = `Dotychczasowa suma wydatków: ${sum} zł`;
								console.log(sum);

								if (sum < monthLimit) {
									info.textContent =
										'Jeszcze nie przekroczyłeś limitu wydatków!';
									info.style.backgroundColor = '#006400';
									info.style.color = '#ffffff';
								} else if (sum == monthLimit) {
									info.textContent =
										'Uważaj, jesteś tuż od przekroczenia limitu wydatków!';
									info.style.backgroundColor = '#3a86ff';
									info.style.color = '#ffffff';
								} else {
									info.textContent =
										'Przekroczyłeś limit miesięcznych wydatków dla tej kategorii!';
									info.style.backgroundColor = '#a4133c';
									info.style.color = '#ffffff';
								}
								sum = 0;
							})
							.catch(err => console.log(err));
					} else if (data[i].name === category && limit == 0) {
						monthlyLimit.textContent = '';
						sumExpenses.textContent = '';
						info.textContent = '';
					}
				}
			})
			.catch(err => console.log(err));
	} else {
		monthlyLimit.classList.add('hidden');
		sumExpenses.classList.add('hidden');
		info.classList.add('hidden');
	}
});
