$(document).ready(function() {
  $('#formModal').validate({
    rules: {
      startDate: {
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
      endDate: {
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
      }
    },
    messages: {
      startDate: {
        required: 'Podaj datę początkową!',
        min: 'Nie możesz wprowadzić daty wcześniejszej niż 01-01-2000!',
        max: 'Data nie może być późniejsza niż dzisiejsza!'
      },
      endDate: {
        required: 'Podaj datę końcową!',
        min: 'Nie możesz wprowadzić daty wcześniejszej niż 01-01-2000!',
        max: 'Data nie może być późniejsza niż dzisiejsza!'
      }
    }
  });
});

function toggleDetails(categoryName) {
  let details = document.getElementById(categoryName + '-details');
  details.style.display = details.style.display === 'none' ? 'block' : 'none';
}