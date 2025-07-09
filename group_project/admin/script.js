//toggle the verified-btn class when the button is clicked.
document.querySelectorAll('.verify-btn').forEach(button => {
    button.addEventListener('click', function () {
        // Add 'verified-btn' class to show the button is verified
        this.classList.add('verified-btn');
        // Optionally, change the button text after verification
        this.textContent = 'Verified';
    });
});

function filterTable() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#applicantsTable tbody tr");

    rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        row.style.display = name.includes(input) ? "" : "none";
    });
}

function switchTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(div => {
        div.style.display = 'none';
    });

    document.querySelectorAll('.tab').forEach(btn => {
        btn.classList.remove('active');
    });

    document.getElementById(tabId).style.display = 'block';
    evt.target.classList.add('active');
}

function fetchUnverified(type, tableId) {
    fetch(`get-unverified.php?type=${type}`)
        .then(res => res.json())
        .then(data => {
            if (!Array.isArray(data)) return console.error("Unexpected data format:", data);
            const tableBody = document.getElementById(tableId).querySelector("tbody");
            tableBody.innerHTML = ""; // clear existing rows
            data.forEach(user => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${user.name}</td>
                    <td>${user.phone_number}</td>
                    <td>${user.email}</td>
                    <td>${user.location}</td>
                    <td><button class="btn" onclick="verifyUser(${user.id}, '${type}', this)">Verify</button></td>
                `;
                tableBody.appendChild(tr);
            });
        })
        .catch(err => console.error("Fetch error:", err));
}

function verifyUser(id, type, btn) {
    fetch("verify-user.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, type })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = btn.closest("tr");
                row.remove();
                alert("Verification successful!");
            } else {
                alert("Verification failed");
            }
        });
}

document.addEventListener("DOMContentLoaded", () => {
    fetchUnverified("student", "verifySTable");
    fetchUnverified("employer", "verifyETable");
});

document.addEventListener("DOMContentLoaded", () => {
    fetch("get-moderation-data.php")
        .then(res => res.json())
        .then(data => {
            populateTable("moderateSTable", data.students, "student");
            populateTable("moderateETable", data.employers, "employer");
            populateTable("jobTable", data.jobs, "job");
        });
});

function populateTable(tableId, data, type) {
    const tbody = document.getElementById(tableId).querySelector("tbody");
    tbody.innerHTML = "";

    data.forEach(row => {
        const tr = document.createElement("tr");

        if (type === "student" || type === "employer") {
            tr.innerHTML = `
                <td>${row.name}</td>
                <td>${row.phone_number}</td>
                <td>${row.email}</td>
                <td>${row.location}</td>
                <td><button class="btn" onclick="deleteEntity(${row.id}, '${type}', this)">Delete</button></td>
            `;
        } else if (type === "job") {
            tr.innerHTML = `
                <td>${row.title}</td>
                <td>${row.tags}</td>
                <td>${row.location}</td>
                <td><button class="btn" onclick="deleteEntity(${row.id}, '${type}', this)">Delete</button></td>
            `;
        }

        tbody.appendChild(tr);
    });
}

function deleteEntity(id, type, btn) {
    if (!confirm("Are you sure you want to delete this " + type + "?")) return;

    fetch("delete-entity.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, type })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.closest("tr").remove();
                alert("Deleted " + type);
            } else {
                alert("Deletion failed.");
            }
        });
}
