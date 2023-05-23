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

    let currentForm = null;
    const toggleFormBtns = document.querySelectorAll('.button-secondary');
    toggleFormBtns.forEach(btn => {
        btn.addEventListener('click', (event) => {
            const form = event.target.nextElementSibling;
            if (form !== currentForm) {
                if (currentForm) {
                    currentForm.style.display = 'none';
                    currentForm.previousElementSibling.style.display = 'block';
                }
                currentForm = form;
            }
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            btn.style.display = 'none';
        });
    });
    
    
function searchdb() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("DBAccountsInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("ftp");
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

