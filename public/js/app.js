const categoryField = document.querySelector('select[name="category"]');
const monthlyLimit = document.querySelector('.monthlyLimit');

categoryField.addEventListener('change', function () {
	if (categoryField.value) {
		const category = categoryField.options[categoryField.selectedIndex].value;
		console.log(category);

		monthlyLimit.textContent = `Miesięczny limit dla kategorii "${category}" to:`;

		/*fetch('/api/limit')
			.then(res => res.json())
			.then(data => console.log(data))
			.catch(err => console.log(err));*/

		monthlyLimit.classList.remove('hidden');
	} else {
		monthlyLimit.classList.add('hidden');
	}
});
