let employerId = null;

async function getEmployerId() {
    try {
        const response = await fetch('get-employer-id.php');
        const data = await response.json();
        if (data.employer_id) {
            employerId = data.employer_id;
            return employerId;
        } else {
            console.error('Failed to get employer ID:', data.error);
            return null;
        }
    } catch (error) {
        console.error('Error fetching employer ID:', error);
        return null;
    }
}

function displayJobs(data) {
    const jobsContainer = document.getElementById("jobsContainer");
    jobsContainer.innerHTML = '';

    if (data.length === 0) {
        jobsContainer.innerHTML = '<p>No jobs found</p>';
        return;
    }

    const table = document.createElement("table");
    table.classList.add("job-table");

    const thead = document.createElement("thead");
    thead.innerHTML = `
        <tr>
            <th>Title</th>
            <th>Location</th>
            <th>Tags</th>
            <th>Actions</th>
        </tr>
    `;
    table.appendChild(thead);

    const tbody = document.createElement("tbody");

    data.forEach(job => {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${job.title}</td>
            <td>${job.location}</td>
            <td>${job.tags}</td>
            <td>
                <a href="edit_job.html?job_id=${job.id}" class="edit-btn">Edit</a>
                <button onclick="deleteJob(${job.id})" class="delete-btn">Delete</button>
            </td>
        `;

        tbody.appendChild(row);
    });

    table.appendChild(tbody);
    jobsContainer.appendChild(table);
}

async function loadJobs() {
    if (!employerId) {
        employerId = await getEmployerId();
        if (!employerId) return;
    }
    
    const sortValue = document.getElementById('sortBy').value;
    let sortBy, sortOrder;
    
    // Parse the sort value to extract field and order
    if (sortValue === 'created_at_asc') {
        sortBy = 'created_at';
        sortOrder = 'ASC';
    } else {
        sortBy = sortValue;
        sortOrder = 'DESC';
    }
    
    fetch(`employer-job-fetch.php?employerId=${employerId}&sort_by=${sortBy}&sort_order=${sortOrder}`)
        .then(res => res.json())
        .then(data => {
            displayJobs(data);
        })
        .catch(error => console.error("Error:", error));
}

function applyForJob(userId, jobId, button) {
    fetch('apply-job.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId, jobId })
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                button.innerText = "Applied";
                button.disabled = true;
                button.classList.add("applied");
            } else {
                console.error("Apply error:", data.message);
            }
        });
}

async function loadApplicants() {
    if (!employerId) {
        employerId = await getEmployerId();
        if (!employerId) return;
    }
    
    const sortValue = document.getElementById('sortBy').value;
    let sortBy, sortOrder;
    
    // Parse the sort value to extract field and order
    if (sortValue === 'created_at_asc') {
        sortBy = 'created_at';
        sortOrder = 'ASC';
    } else {
        sortBy = sortValue;
        sortOrder = 'DESC';
    }
    
    fetch(`load-applicants.php?employer_id=${employerId}&sort_by=${sortBy}&sort_order=${sortOrder}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#applicantsTable tbody");
            tbody.innerHTML = "";

            data.forEach(app => {
                const row = document.createElement("tr");

                const statusOptions = ['Applied', 'Seen', 'Approved', 'Rejected', 'Completed']
                    .map(status => `<option value="${status}" ${app.status === status ? "selected" : ""}>${status}</option>`)
                    .join("");

                row.innerHTML = `
                    <td>${app.title}</td>
                    <td>${app.name}</td>
                    <td>${app.email}</td>
                    <td>
                      <select onchange="updateStatus(${app.id}, this.value)">
                        ${statusOptions}
                      </select>
                    </td>
                  `;
                tbody.appendChild(row);
            });
        })
        .catch(err => console.error("Error loading applicants:", err));
}

function updateStatus(applicationId, newStatus) {
    fetch("update-application-status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ applicationId, newStatus })
    })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert("Status updated");
            } else {
                alert("Failed to update status");
            }
        })
        .catch(err => console.error("Error updating status:", err));
}

function filterTable() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#applicantsTable tbody tr");

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        row.style.display = name.includes(input) ? "" : "none";
    });
}

async function loadPreviousWorkers() {
    if (!employerId) {
        employerId = await getEmployerId();
        if (!employerId) return;
    }
    
    fetch(`load-previous-workers.php?employer_id=${employerId}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#analyticsTable tbody");
            tbody.innerHTML = "";

            data.forEach(worker => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${worker.worker_name}</td>
                    <td>${worker.job_title}</td>
                    <td><a href="review.html?job_id=${worker.job_id}&worker_id=${worker.worker_id}&worker_name=${encodeURIComponent(worker.worker_name)}"><button class="rate-btn">Rate</button></a></td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            console.error("Failed to load previous workers:", err);
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

    if (tabId === 'analytics') {
        loadPreviousWorkers();
    }
}

function initRatingStars() {
    const urlParams = new URLSearchParams(window.location.search);
    const jobId = urlParams.get('job_id');
    const workerId = urlParams.get('worker_id');
    const workerName = urlParams.get('worker_name');

    document.querySelector("h2").textContent = `Rate ${workerName}`;

    let selectedRating = 1;
    const stars = document.querySelectorAll('.star');

    stars.forEach((star, index) => {
        star.addEventListener('mouseover', () => highlightStars(index + 1));
        star.addEventListener('mouseout', () => highlightStars(selectedRating));
        star.addEventListener('click', () => {
            selectedRating = index + 1;
            highlightStars(selectedRating);
        });
    });

    function highlightStars(rating) {
        stars.forEach((star, index) => {
            star.classList.toggle('selected', index < rating);
            star.classList.toggle('hover', index < rating);
        });
    }

    window.submitReview = async function () {
        if (!employerId) {
            employerId = await getEmployerId();
            if (!employerId) return;
        }
        
        const comment = document.querySelector("textarea").value;

        fetch("submit-review.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                job_id: jobId,
                reviewer_id: employerId,
                reviewer_type: 'employer',
                reviewee_id: workerId,
                reviewee_type: 'student',
                rating: selectedRating,
                comment: comment
            })
        })
            .then(res => res.json())
            .then(data => {
                alert("Review submitted!");
                window.location.href = "applicants.html";
            })
            .catch(err => {
                console.error("Error submitting review:", err);
                alert("Error submitting review.");
            });
    };
}

window.initRatingStars = initRatingStars;
window.deleteJob = deleteJob;

window.addEventListener('DOMContentLoaded', async () => {

    employerId = await getEmployerId();
    if (!employerId) {
        console.error('Failed to get employer ID');
        return;
    }
    
    loadJobs();
    loadApplicants();
    
    fetch(`greeting.php?employer_id=${employerId}`)
        .then(res => res.json())
        .then(data => {
            const greeting = document.querySelector(".greeting");
            if (greeting) {
                greeting.querySelector("h1").innerHTML = `👋<br>Hello, ${data.name} • ${data.rating ?? 'N/A'} ★`;

                if (data.is_verified === 0) {
                    const warning = document.createElement("p");
                    warning.textContent = "Your account has not been verified.";
                    warning.style.color = "red";
                    warning.style.fontStyle = "italic";
                    greeting.appendChild(warning);

                    const addJobBtn = document.querySelector(".add-job-btn");
                    if (addJobBtn) {
                        addJobBtn.classList.add("disabled");
                    }
                }
            }
        })
        .catch(err => {
            console.error("Failed to load greeting:", err);
        });
});

document.addEventListener("DOMContentLoaded", async () => {
    
    if (!employerId) {
        employerId = await getEmployerId();
        if (!employerId) return;
    }
    
    const nameInput = document.getElementById("full-name");
    const phoneInput = document.getElementById("phone-number");
    const emailInput = document.getElementById("email");
    const locationSelect = document.getElementById("location");
    const genderSelect = document.getElementById("gender");
    const saveBtn = document.querySelector(".save-btn");

    fetch(`get-employer-profile.php?id=${employerId}`)
        .then(res => res.json())
        .then(data => {
            nameInput.value = data.name || "";
            phoneInput.value = data.phone_number || "";
            emailInput.value = data.email || "";
            locationSelect.value = data.location || "KTDI";
            genderSelect.value = data.gender || "male";
        })
        .catch(err => console.error("Error fetching profile:", err));

    saveBtn.addEventListener("click", (e) => {
        e.preventDefault();

        const formData = {
            id: employerId,
            full_name: nameInput.value,
            phone: phoneInput.value,
            email: emailInput.value,
            location: locationSelect.value,
            gender: genderSelect.value
        };

        fetch("update-employer-profile.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData)
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert("Profile updated successfully!");
                } else {
                    alert("Failed to update profile.");
                }
            })
            .catch(err => {
                console.error("Update error:", err);
                alert("Something went wrong!");
            });
    });
});

function deleteJob(jobId) {
    if (confirm('Are you sure you want to delete this job? This action cannot be undone.')) {
        fetch('delete-job.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ job_id: jobId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Job deleted successfully!');
                loadJobs(); // Refresh the jobs list
            } else {
                alert('Failed to delete job: ' + data.message);
            }
        })
        .catch(err => {
            console.error('Error deleting job:', err);
            alert('Error deleting job');
        });
    }
}