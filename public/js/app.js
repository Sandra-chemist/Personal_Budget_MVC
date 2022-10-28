const categoryField = document.querySelector('select[name="category"]');
const monthlyLimit = document.querySelector('.monthlyLimit');

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
					}
				}
			})
			.catch(err => console.log(err));

		monthlyLimit.classList.remove('hidden');
	} else {
		monthlyLimit.classList.add('hidden');
	}
});
