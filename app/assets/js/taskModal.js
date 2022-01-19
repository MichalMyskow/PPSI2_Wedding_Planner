let modal = require("./modal");

let baseUrl = `${window.location.origin}`;
let editTaskBtns = document.querySelectorAll(".edit-task-btn");

let editForm = document.querySelector('#edit_task_form');
let nameInput = editForm.elements['name'];
let personInput = editForm.elements['person'];
let idInput = editForm.elements['task_id'];
let editSubmit = document.querySelector('#edit_submit');

let editModal = document.querySelector("#edit-modal");

function showEditModal(e) {
    editModal.classList.add('active');
}
function hideEditModal(e) {
    editModal.classList.remove('active');
}

window.addEventListener("click", function(event){
    if (event.target == editModal) {
        hideEditModal();
    }
});
window.addEventListener("load", function(){
    document.querySelector("#add-task-btn").addEventListener("click", function(){
        modal.showModal();
    }
    );
});

editTaskBtns.forEach(btn => btn.addEventListener('click', function(){
    var xhr = new XMLHttpRequest();
    xhr.open('GET', `${baseUrl}/task/show/${this.dataset.id}`, true);
    xhr.addEventListener("loadstart", function(){
        document.querySelector("#loader").classList.add('active');
    });
    xhr.addEventListener("loadend", function(){
        document.querySelector("#loader").classList.remove('active');
        showEditModal();

    });
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        var result = JSON.parse(this.responseText);
        nameInput.value=result.name;
        personInput.value=result.person;
        idInput.value=result.id;
    };
    xhr.send();
    
}));



editSubmit.addEventListener("click", function(e){
    var task = document.querySelector(`#task-${idInput.value}`);

    e.preventDefault();
    var data = {};
    data.name = nameInput.value;
    data.person = personInput.value;
    var json = JSON.stringify(data);

    var xhr = new XMLHttpRequest();
    xhr.open('PUT', `${baseUrl}/task/update/${idInput.value}`, true);
    xhr.addEventListener("loadstart", function(){
        document.querySelector("#loader").classList.add('active');
    });
    xhr.addEventListener("loadend", function(){
        document.querySelector("#loader").classList.remove('active');

    });
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        var result = JSON.parse(this.responseText);
        if (xhr.readyState == 4 && xhr.status == "200" && result.status === "success") {
            task.querySelectorAll('.checklist__name')[0].innerHTML = nameInput.value;
            task.querySelectorAll('.checklist__person')[1].innerHTML = personInput.value;
            hideEditModal();
        }
    };
    xhr.send(json);
});