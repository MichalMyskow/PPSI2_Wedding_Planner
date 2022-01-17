let guestForm = document.querySelector("#guest_placement_form");
let guestFormInput = guestForm.elements['guest'];

let seats = document.querySelectorAll(".guest-placement__seat");
let unsetBtns = document.querySelectorAll(".guest-placement__list-unset-btn");
let saveBtn = document.querySelector("#guest-placement__save-btn");
let clearAllBtn = document.querySelector("#guest-placement__clear-all-btn");

let currentGuest = '';
let currentSeat = '';

function markSelectSeat(object){
    for (seat of seats){
        if(seat === object)
            seat.classList.toggle('selected');
        else
            seat.classList.remove('selected');
    }
}
function unmarkSeats(object){
    for (seat of seats){
        seat.classList.remove('selected');
    }
}
function isSeatOccupied(object){
    if(object.classList.contains('occupied'))
        return true;
    else
        return false;
}
function isSeatSelected(object){
    if(object.classList.contains('selected'))
        return true;
    else
        return false;
}
function isGuestHaveSeat(guest){
    if(guest.dataset.guestSeatId){
        return true;
    }else
        return false;
}
function getGuestSeat(guest){
    return document.querySelectorAll(`.guest-placement__seat[data-seat-id="${guest.dataset.guestSeatId}"]`)[0];

}
function getGuestBySeat(seat){
    return document.querySelectorAll(`.guest[data-guest-seat-id="${seat.dataset.seatId}"]`)[0];

}
function showGuestBySeat(seat){
    guest = getGuestBySeat(seat);
    guest.checked = true;     
    guest.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'center'
    });
}
function changeSelectedSeatToOccupied(){
    currentSeat.classList.remove('selected');
    currentSeat.classList.add('occupied');
}
function setCurrentGuest(guest){
    currentGuest = guest;
}
function resetCurrentSeat(){
    currentSeat = '';
}
function resetCurrentGuest(){
    currentGuest = '';
}
function setCurrentSeat(seat){
    currentSeat = seat;
}
function unselectCurrentGuest(){
    currentGuest.checked = false;
}
function clearData(){
    unmarkSeats();
    unselectCurrentGuest();
    resetCurrentGuest();
    resetCurrentSeat();
}
function unsetGuestSeat(guest){
    if(seat = getGuestSeat(guest)){
        seat.classList.remove('selected');
        seat.classList.remove('occupied');
        guest.dataset.guestSeatId = '';
        clearData();
    }
}
function savePlan(){
    let savingPlan = [];
    let savingObject;
        guestFormInput.forEach((guest) => {
        savingObject = { id: parseInt(guest.dataset.guestId, 10), seatNumber: parseInt(guest.dataset.guestSeatId, 10) };
        savingPlan.push(savingObject);
    });
    let plan = JSON.stringify(savingPlan, null, 2);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', `${window.location.origin}/guest-placement/save`, true);
        xhr.addEventListener("loadstart", function(){
            document.querySelector("#loader-content").innerHTML = "Ładowanie";
            document.querySelector("#loader").classList.add('active');
        });
        xhr.addEventListener("loadend", function(){
            document.querySelector("#loader-content").innerHTML = "Zapisano";
            setTimeout(() => {document.querySelector("#loader").classList.remove('active');}, 2000);
        });
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            // console.log(this.response);
        };
        xhr.send(plan);  
}
function clearPlan(){
    for (guest of guestFormInput) {
        guest.dataset.guestSeatId = '';
    }
    for (seat of seats){
        seat.classList.remove('occupied');
    }
    clearData();
}

guestFormInput.forEach(guest => guest.addEventListener('click', function(){
    if(currentGuest === guest && guest.checked == true){
        guest.checked = false;
        resetCurrentGuest();        
        unmarkSeats();        
        resetCurrentSeat();
    }
    else{
    setCurrentGuest(guest);
    seat = getGuestSeat(guest);
    if(seat){  
        resetCurrentSeat();
        markSelectSeat(seat);
        if(isSeatSelected(seat) && isSeatOccupied(seat)){
            setCurrentSeat(seat);
            showGuestBySeat(seat);
            setCurrentGuest(getGuestBySeat());
        }
    }else if(currentSeat && !isSeatOccupied(currentSeat) && !isGuestHaveSeat(currentGuest)){
        changeSelectedSeatToOccupied();
        currentGuest.dataset.guestSeatId = currentSeat.dataset.seatId;
        clearData();
    }
    else{
        clearData();
    }
}
}));
seats.forEach(seat => seat.addEventListener('mouseover', function(){ 
        
        if(currentGuest){
            clearData();
        }
        if(!currentSeat && isSeatOccupied(seat)){
            showGuestBySeat(seat);
        }
        
}));
seats.forEach(seat => seat.addEventListener('click', function(){ 
        
        if(currentGuest){
            clearData();
        }
        else{
            resetCurrentSeat();
            markSelectSeat(seat);
        
            if(isSeatSelected(seat)){
                setCurrentSeat(seat);
            }
        }

}));

unsetBtns.forEach(a => a.addEventListener('click', function(){
    let guest = document.querySelectorAll(`.guest[data-guest-id="${a.dataset.deleteGuestId}"]`)[0];
    unsetGuestSeat(guest);
}));
saveBtn.addEventListener('click', function(){
    savePlan();
});
clearAllBtn.addEventListener('click', function(){
    clearPlan();
});
