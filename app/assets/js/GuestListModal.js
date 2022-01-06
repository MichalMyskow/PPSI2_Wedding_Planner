let modal = document.querySelector("#modal");
// var btn = document.querySelector("#button");

function showModal(e) {
    modal.classList.add('active');
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.classList.remove('active');
    }
}

window.addEventListener("load", function(){
    document.querySelector("#add-guest-btn").addEventListener("click", showModal);
});
window.addEventListener("load", function(){
    document.querySelectorAll(".guest-list__item-action--edit").forEach(btn => btn.addEventListener("click", showModal));
});
