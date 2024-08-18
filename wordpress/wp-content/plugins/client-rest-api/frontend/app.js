document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('client-form');
    const clientList = document.getElementById('client-list');
    //const clientIdField = document.getElementById('client_id');
    const custom1Field = document.getElementById('custom_1');
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    const birthdayField = document.getElementById('birthday');

    const API_URL = '/wp-json/clientmanager/v1/clients/';

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        //const clientId = clientIdField.value;
        const custom1 = custom1Field.value;
        const clientData = {
            custom_1: custom1Field.value,// maybe remove this line
            name: nameField.value,
            email: emailField.value,
            birthday: birthdayField.value
        };

        if (custom1) {
            updateClient(custom1, clientData);
        } else {
            createClient(clientData);
        }
    });

    function fetchClients() {
        fetch(API_URL)
            .then(response => response.json())
            .then(data => {
                clientList.innerHTML = '';
                data.forEach(client => {
                    const li = document.createElement('li');
                    li.textContent = `${client.name} (${client.email})`;
                    const editBtn = document.createElement('button');
                    editBtn.textContent = 'Edit';
                    editBtn.addEventListener('click', () => loadClient(client));
                    const deleteBtn = document.createElement('button');
                    deleteBtn.textContent = 'Delete';
                    deleteBtn.addEventListener('click', () => deleteClient(client.custom_1));
                    li.appendChild(editBtn);
                    li.appendChild(deleteBtn);
                    clientList.appendChild(li);
                });
            });
    }

    function createClient(clientData) {
        fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(clientData)
        }).then(response => response.json())
          .then(() => {
              form.reset();
              fetchClients();
          });
    }

    function updateClient(custom1, clientData) {
        fetch(`${API_URL}?custom_1=${custom1}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(clientData)
        }).then(response => response.json())
          .then(() => {
              form.reset();
              fetchClients();
          });
    }

    function deleteClient(custom1) {
        fetch(`${API_URL}?custom_1=${custom1}`, {
            method: 'DELETE'
        }).then(() => fetchClients());
    }

    function loadClient(client) {
        //clientIdField.value = client.id;
        custom1Field.value = client.custom_1;
        nameField.value = client.name;
        emailField.value = client.email;
        birthdayField.value = client.birthday;
    }

    fetchClients();
});

