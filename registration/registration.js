const addBtn = document.getElementById('addRegBtn');

let ulDisc = document.getElementsByClassName('disciplins-ul');

addBtn.addEventListener(e => {
    e.preventDefault();
})

function addDisc() {

    const text = document.getElementById('discInput');
    const li = document.createElement('li');
    li.value = text
    ulDisc.appendChild(li);
}