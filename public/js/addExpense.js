$(document).ready(function() {

  $('#formExpense').validate({
      rules: {
          amount: {
            required: true,
            min: 0.01
          },
          date: {
            required: true,
            min: '2000-01-01',
            max: function() {
                let today = new Date();
                let year = today.getFullYear();
                let month = today.getMonth() + 1; 
                let day = today.getDate();
                if (month < 10) {
                    month = "0" + month; 
                }
                if (day < 10) {
                    day = "0" + day; 
                }
                return year + '-' + month + '-' + day;
            }
          },
          paymentMethod: 'required',
          category : 'required'
      },
      messages: {
          amount: {
            required: 'Podaj kwotę wydatku!',
            min: 'Kwota musi wynosić conajmniej 0.01 zł!'
          },
          date: {
            required: 'Wybierz datę',
            min: 'Nie możesz wprowadzić daty wcześniejszej niż 01-01-2000!',
            max: 'Data nie może być późniejsza niż dzisiejsza!'
          },
          paymentMethod: 'Wybierz rodzaj płatności!',
          category: 'Wybierz kategorię!'
      }
  });
});

let categoryField = document.getElementById('selectExpenseCategory');

let dateField = document.querySelector('.selectExpenseDate');

let amountField = document.querySelector('.selectAmount');

categoryField.addEventListener('change', async (event) => {
  let selectedCategory = event.target.value;

  let limit = await getLimitForCategory(selectedCategory);

  if (limit !== null && limit !== undefined) {
      document.getElementById('limitField').innerHTML = `Miesięczny limit dla tej kategorii wynosi: ${limit} zł`;
  } else {
     document.getElementById('limitField').innerHTML = 'Limit dla tej kategorii nie został ustalony.';
  }
  });

  dateField.addEventListener('change', async (event) => {
    let selectedDate = event.target.value;
  
    let selectedCategory = categoryField.value;
  
    let monthlyExpenses = await getMonthlyExpensesForCategory(selectedCategory, selectedDate);
  
    if (monthlyExpenses !== null && monthlyExpenses !== undefined) {
        document.getElementById('limitValue').innerHTML = `Wydatki w tym miesiącu wynoszą ${monthlyExpenses} zł`;
    } else {
        document.getElementById('limitValue').innerHTML = 'W tym miesiącu nie masz wydatków w tej kategorii!';
    }
    });

amountField.addEventListener('input', async (event) => {
  let selectedAmount = event.target.value;

  let selectedCategory = categoryField.value

  let selectedDate = dateField.value;

  let limit = await getLimitForCategory(selectedCategory);

  if (limit !== null && limit !== undefined) {

    let monthlyExpenses = await getMonthlyExpensesForCategory(selectedCategory, selectedDate);

    let balance = limit - monthlyExpenses - selectedAmount;

    if(balance < 0){

      document.getElementById('limitAfterOperation').innerHTML = `<span style="color: red;">Po dodaniu tego wydatku twój limit wynosić będzie: ${balance} zł </span>`;
    }else{
      document.getElementById('limitAfterOperation').innerHTML = `Po dodaniu tego wydatku twój limit wynosić będzie: ${balance} zł `;
    }
  }
});

const getLimitForCategory = async (category) => {
  try{
    const res = await fetch(`../api/limit/${category}`);
    const data = await res.json();
    return data;
  }catch(e){
    console.log('ERROR, e');  
  }
  };

const getMonthlyExpensesForCategory = async (category, date) => {
  try{
    const res = await fetch(`../api/limit/${category}/${date}`);
    const data = await res.json();
    return data;
  }catch(e){
    console.log('ERROR, e');  
  }
  };