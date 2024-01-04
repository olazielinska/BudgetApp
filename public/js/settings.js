function incomeSettings() {

  let list = document.getElementById("incomeSettingsList");
  let form = document.getElementById("formIncomeCategory");
  let isVisible = list.style.display === "block";

  list.style.display = isVisible ? "none" : "block";
  form.style.display = isVisible ? "none" : "block";
}

function expenseSettings() {

  let list = document.getElementById("expenseSettingsList");
  let form = document.getElementById("formExpenseCategory");
  let isVisible = list.style.display === "block";

  list.style.display = isVisible ? "none" : "block";
  form.style.display = isVisible ? "none" : "block";
}

function paymentMethodSettings() {

  let list = document.getElementById("paymentMethodSettingsList");
  let form = document.getElementById("formPaymentMethod");

  let isVisible = list.style.display === "block";

  list.style.display = isVisible ? "none" : "block";
  form.style.display = isVisible ? "none" : "block";
}

document.querySelectorAll('#removeIncomeCategoryIcon').forEach(function (icon) {
    icon.addEventListener('click', function () {

    let categoryId = icon.getAttribute('data-category-id');

    document.getElementById("hiddenRemoveIncomeCategoryInput").value = categoryId;

    $('#removeIncomeCategoryModal').modal('show');
    });
});

document.querySelectorAll('#editIncomeCategoryIcon').forEach(function (icon){
  icon.addEventListener('click', function () {

    let categoryId = icon.getAttribute('data-category-id');
    let categoryName = icon.getAttribute('name');

    document.getElementById('incomeCategoryNameInput').value = categoryName;

    document.getElementById('hiddenEditIncomeCategoryInput').value = categoryId;

    $('#editIncomeCategoryModal').modal('show');
  });
});

document.querySelectorAll('#removeExpenseCategoryIcon').forEach(function (icon) {
    icon.addEventListener('click', function () {

    let categoryId = icon.getAttribute('data-category-id');

    document.getElementById("hiddenRemoveExpenseCategoryInput").value = categoryId;

    $('#removeExpenseCategoryModal').modal('show');
    });
});

document.querySelectorAll('#editExpenseCategoryIcon').forEach(function (icon){
  icon.addEventListener('click', function () {

    let categoryId = this.getAttribute('data-category-id');
    let categoryName = this.getAttribute('name');

    document.getElementById('expenseCategoryNameInput').value = categoryName;

    document.getElementById('hiddenEditExpenseCategoryInput').value = categoryId;

    $('#editExpenseCategoryModal').modal('show');
  });
});


document.querySelectorAll('.limit-icon').forEach(function (icon) {
    icon.addEventListener('click', function () {

    let categoryId = icon.getAttribute('data-category-id');

    let categoryLimit = icon.getAttribute('name');

    document.getElementById("expenseLimitCategoryModalHiddenInput").value = categoryId;
    document.getElementById("expenseLimitCategoryModalInput").value = categoryLimit;

    $('#expenseCategoryLimitModal').modal('show');
    });
});

document.querySelectorAll('#removePaymentMethodIcon').forEach(function (icon) {
    icon.addEventListener('click', function () {

    let methodId = icon.getAttribute('data-method-id');

    document.getElementById("hiddenRemovePaymentMethodInput").value = methodId;

    $('#removePaymentMethodModal').modal('show');
    });
});

document.querySelectorAll('#editPaymentMethodIcon').forEach(function (icon){
  icon.addEventListener('click', function () {

    let methodId = icon.getAttribute('data-method-id');
    let methodName = icon.getAttribute('name');

    document.getElementById('paymentMethodNameInput').value = methodName;

    document.getElementById('hiddenEditPaymentMethodInput').value = methodId;

    $('#editPaymentMethodModal').modal('show');
  });
});


function showUserProfile() {
    var list = document.getElementById('userProfileList');
    list.classList.toggle('d-none');
}

$(document).ready(function() {

$('#formEdit').validate({
  rules: {
      newPassword: {
          required: true,
          minlength: 6,
          validPassword: true
      }
  },
  messages: {
      newPassword: {
        required: 'Hasło jest wymagane',
        minlength: 'Hasło wymaga conajmniej 6 znaków'
      }
  }
});
});

$(document).ready(function () {

$('#formCategoryLimit').validate({
  rules: {
      categoryLimit: {
        required: true,
        min: 1
      }
  },
  messages:{
    categoryLimit: {
      required: 'Kwota limitu jest wymagana',
      min: 'Kwota nie może być mniejsza niż 1 zł'
    }
  }
});
});

document.querySelectorAll('.cancelButton').forEach(function (button) {
button.addEventListener('click', function () {
    $('#editIncomeCategoryModal').modal('hide');
    $('#editExpenseCategoryModal').modal('hide');
    $('#removeIncomeCategoryModal').modal('hide');
    $('#removeExpenseCategoryModal').modal('hide');
    $('#removePaymentMethodModal').modal('hide');
    $('#editPaymentMethodModal').modal('hide');
    $('#expenseCategoryLimitModal').modal('hide');
});
});