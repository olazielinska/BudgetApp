
let today = new Date();
let dd = String(today.getDate()).padStart(2, '0');
let mm = String(today.getMonth() + 1).padStart(2, '0');
let yyyy = today.getFullYear();
let currentDate = yyyy + '-' + mm + '-' + dd;

document.getElementById('currentDate').value = currentDate;

$(document).ready(function() {

    $('#formIncome').validate({
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
            category: 'Wybierz kategorię!'
        }
    });
});