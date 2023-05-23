const showUserFormLink = document.getElementById('show-user-form-link');
const hideUserFormLink = document.getElementById('hide-user-form-link');
const createUserFormContainer = document.getElementById('create-user-form-container');

showUserFormLink.addEventListener('click', () => {
  createUserFormContainer.style.display = 'block';
  showUserFormLink.style.display = 'none';
});

hideUserFormLink.addEventListener('click', () => {
  createUserFormContainer.style.display = 'none';
  showUserFormLink.style.display = 'inline-block';
});

const showChangeFormLink = document.getElementById('show-change-form-link');
const hideChangeFormLink = document.getElementById('hide-change-form-link');
const changeUserFormContainer = document.getElementById('change-user-form-container');

showChangeFormLink.addEventListener('click', () => {
  changeUserFormContainer.style.display = 'block';
  showChangeFormLink.style.display = 'none';
});

hideChangeFormLink.addEventListener('click', () => {
  changeUserFormContainer.style.display = 'none';
  showChangeFormLink.style.display = 'inline-block';
});

const showAssignFormLink = document.getElementById('show-assign-form-link');
const hideAssignFormLink = document.getElementById('hide-assign-form-link');
const createAssignFormContainer = document.getElementById('create-assign-form-container');

showAssignFormLink.addEventListener('click', () => {
  createAssignFormContainer.style.display = 'block';
  showAssignFormLink.style.display = 'none';
});

hideAssignFormLink.addEventListener('click', () => {
  createAssignFormContainer.style.display = 'none';
  showAssignFormLink.style.display = 'inline-block';
});


const showRemoveFormLink = document.getElementById('show-remove-form-link');
const hideRemoveFormLink = document.getElementById('hide-remove-form-link');
const createRemoveFormContainer = document.getElementById('create-remove-form-container');

showRemoveFormLink.addEventListener('click', () => {
  createRemoveFormContainer.style.display = 'block';
  showRemoveFormLink.style.display = 'none';
});

hideRemoveFormLink.addEventListener('click', () => {
  createRemoveFormContainer.style.display = 'none';
  showRemoveFormLink.style.display = 'inline-block';
});

const showFormLink = document.getElementById('show-form-link');
const hideFormLink = document.getElementById('hide-form-link');
const createEmailFormContainer = document.getElementById('create-db-form-container');

showFormLink.addEventListener('click', () => {
  createEmailFormContainer.style.display = 'block';
  showFormLink.style.display = 'none';
});

hideFormLink.addEventListener('click', () => {
  createEmailFormContainer.style.display = 'none';
  showFormLink.style.display = 'inline-block';
});

function searchdb() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("DBAccountsInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("baze");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}