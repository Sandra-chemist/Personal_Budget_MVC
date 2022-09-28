google.charts.load('current', { 'packages': ['corechart'] });
google.charts.setOnLoadCallback(drawIncomeChart);

function drawIncomeChart() {

    var incomeData = google.visualization.arrayToDataTable([
        ['Task', 'Hours per Day'],
        ['Work', 11],
        ['Eat', 2],
        ['Commute', 2],
        ['Watch TV', 2],
        ['Sleep', 7]
    ]);

    var options = {
        title: 'Przychody'
    };

    var chart = new google.visualization.PieChart(document.getElementById('incomePiechart'));

    chart.draw(incomeData, options);
}
google.charts.setOnLoadCallback(drawExpenseChart);

function drawExpenseChart() {

    var expenseData = google.visualization.arrayToDataTable([
        ['Task', 'Hours per Day'],
        ['Work', 11],
        ['Eat', 2],
        ['Commute', 2],
        ['Watch TV', 2],
        ['Sleep', 7]
    ]);

    var options = {
        title: 'Wydatki'
    };

    var chart = new google.visualization.PieChart(document.getElementById('expensePiechart'));

    chart.draw(expenseData, options);
}