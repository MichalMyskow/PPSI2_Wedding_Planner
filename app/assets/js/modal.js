let modal = document.querySelector("#modal");
// var btn = document.querySelector("#button");

function showModal(e) {
    modal.classList.add('active');
}
function hideModal(e) {
    modal.classList.remove('active');
}
window.addEventListener("click", function(event){
    if (event.target == modal) {
        hideModal();
    }
});

module.exports = {
    showModal,
    hideModal
};