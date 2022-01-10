let taskItems = document.querySelectorAll(".checklist__item");
let inputs = document.querySelectorAll(".checklist__input");
let editTaskBtns = document.querySelectorAll(".edit-task");
let deleteTaskBtns = document.querySelectorAll(".delete-task-btn");
let baseUrl = `${window.location.origin}`;
let url = ``;
// console.log(url);
inputs.forEach(btn => btn.addEventListener('change', function() {
    if (this.checked) {
        url = `${baseUrl}/task/complete/${this.dataset.id}`
    } else {
        url = `${baseUrl}/task/cancel/${this.dataset.id}`

    }

    var xhr = new XMLHttpRequest();
    xhr.open('PUT', url, true);
    xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
    xhr.onload = function() {

    };
    xhr.send();

}));
deleteTaskBtns.forEach(btn => btn.addEventListener('click', function() {
    var element = this;
    var xhr = new XMLHttpRequest();
    xhr.open('DELETE', `${baseUrl}/task/delete/${this.dataset.id}`, true);
    xhr.addEventListener("loadstart", function(){
        document.querySelector("#loader").classList.add('active');
    });
    xhr.addEventListener("loadend", function(){
        document.querySelector("#loader").classList.remove('active');
        modal.showModal();

    });
    xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
    xhr.onload = function() {
        var result = JSON.parse(this.responseText);
        if (xhr.readyState == 4 && xhr.status == "200") {
            element.parentNode.parentNode.parentNode.removeChild(element.parentNode.parentNode);
        }
    };
    xhr.send();

}));
