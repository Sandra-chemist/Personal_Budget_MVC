const categoryField = document.querySelector('select[name="category"]');
const monthlyLimit = document.querySelector('.monthlyLimit');

const date = new Date();
const currentMonth = date.getMonth() + 1;
//console.log(currentMonth);
const nextMonth = date.getMonth() + 2;
//console.log(nextMonth);
const currentYear = date.getFullYear();
//console.log(currentYear);

const currentMonthStart = currentYear + '-' + currentMonth + '-' + '01';
console.log(currentMonthStart);

if (currentMonth === 12) {
	year = currentYear + 1;
} else {
	year = currentYear;
}
const nextMonthStart = year + '-' + nextMonth + '-' + '01';
console.log(nextMonthStart);

let sum = 0;

categoryField.addEventListener('change', function () {
	if (categoryField.value) {
		const category = categoryField.options[categoryField.selectedIndex].value;
		console.log(category);

		/*fetch('/expense/limit')
			.then(res => res.json())
			.then(data => console.log(data))
			.catch(err => console.log(err));*/

		fetch('/expense/limit')
			.then(res => res.json())
			.then(data => {
				for (let i = 0; i < data.length; i++) {
					if (data[i].name === category && data[i].monthly_limit !== 0) {
						monthlyLimit.textContent = `Miesięczny limit dla tej kategorii wynosi: ${data[i].monthly_limit} zł`;
						console.log(`${data[i].monthly_limit}`);
					} else if (data[i].name === category && data[i].monthly_limit === 0) {
						monthlyLimit.textContent = '';
					}
				}
			})
			.catch(err => console.log(err));

		fetch('/expense/sumMonthlyExpenses')
			.then(res => res.json())
			.then(expenses => {
				for (let j = 0; j < expenses.length; j++) {
					if (
						expenses[i].name === category &&
						expenses[j].date_of_expense >= currentMonthStart &&
						expenses[j].date_of_expense < nextMonthStart
					) {
						console.log(expenses);
						console.log(`${expenses[j].date_of_expense} ${expenses[j].amount}`);
						sum = sum + parseFloat(expenses[j].amount);
					}
					console.log(sum);
				}
			})
			.catch(err => console.log(err));

		monthlyLimit.classList.remove('hidden');
	} else {
		monthlyLimit.classList.add('hidden');
	}
});
