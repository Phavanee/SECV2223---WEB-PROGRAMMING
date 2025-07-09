let studentId = null;

async function getStudentId() {
    try {
        const response = await fetch('get-student-id.php');
        const data = await response.json();
        if (data.student_id) {
            studentId = data.student_id;
            console.log('Student ID fetched:', studentId); 
            return studentId;
        } else {
            console.error('Failed to get student ID:', data.error);
            return null;
        }
    } catch (error) {
        console.error('Error fetching student ID:', error);
        return null;
    }
}

function displayJobs(data, appliedJobIds = []) {
    const jobsContainer = document.getElementById("jobsContainer");
    jobsContainer.innerHTML = '';

    if (data.length === 0) {
        jobsContainer.innerHTML = '<p>No jobs found</p>';
    } else {
        data.forEach(job => {
            const jobCard = document.createElement("div");
            jobCard.classList.add("job-card");

            const contentDiv = document.createElement("div");
            contentDiv.classList.add("job-info");

            const buttonDiv = document.createElement("div");
            buttonDiv.classList.add("apply-btn-container");

            const title = document.createElement("h3");
            title.textContent = job.title;

            const location = document.createElement("p");
            location.textContent = job.location;

            const tagsContainer = document.createElement("div");
            tagsContainer.classList.add("tags-container");
            job.tags.split(',').forEach(tag => {
                const tagElement = document.createElement("span");
                tagElement.classList.add("tag");
                tagElement.innerText = tag.trim();
                tagsContainer.appendChild(tagElement);
            });

            contentDiv.appendChild(title);
            contentDiv.appendChild(location);
            contentDiv.appendChild(tagsContainer);

            const applyButton = document.createElement("button");

            if (appliedJobIds.includes(parseInt(job.id))) {
                applyButton.innerText = "Applied";
                applyButton.disabled = true;
                applyButton.classList.add("applied");
            } else {
                applyButton.innerText = "Apply Now";
                applyButton.onclick = () => applyForJob(studentId, job.id, applyButton);
            }

            buttonDiv.appendChild(applyButton);

            jobCard.appendChild(contentDiv);
            jobCard.appendChild(buttonDiv);

            jobsContainer.appendChild(jobCard);
        });
    }
}

async function loadJobs() {
    if (!studentId) {
        studentId = await getStudentId();
        if (!studentId) return;
    }
    
    let appliedJobIds = [];
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

    fetch(`get-applications.php?userId=${studentId}`)
        .then(res => res.json())
        .then(applied => {
            appliedJobIds = applied;
            return fetch(`job-fetch.php?sort_by=${sortBy}&sort_order=${sortOrder}`);
        })
        .then(response => response.json())
        .then(data => {
            displayJobs(data, appliedJobIds);
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

async function loadStudentApplications() {
    if (!studentId) {
        studentId = await getStudentId();
        if (!studentId) return;
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
    
    fetch(`load-student-applications.php?sort_by=${sortBy}&sort_order=${sortOrder}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#studentApplicationsTable tbody");
            tbody.innerHTML = "";

            data.forEach(app => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${app.title}</td>
                    <td>${app.location}</td>
                    <td>${app.tags}</td>
                    <td>${app.status}</td>
                    <td>
                        <button class="remove-btn" onclick="removeApplication(${app.application_id}, this)">
                            Remove
                        </button>
                    </td>`;
                tbody.appendChild(row);
            });
        });
}

function removeApplication(applicationId, button) {
    if (!confirm('Are you sure you want to remove this application?')) {
        return;
    }
    
    fetch('remove-application.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ application_id: applicationId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove the row from the table
            button.closest('tr').remove();
            alert('Application removed successfully!');
        } else {
            alert('Failed to remove application: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Error removing application:', err);
        alert('Error removing application');
    });
}

function filterTable() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#studentApplicationsTable tbody tr");

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

    if (tabId === 'completed-tab') {
        loadPreviousJobs();
    }
}

async function loadPreviousJobs() {
    if (!studentId) {
        studentId = await getStudentId();
        if (!studentId) return;
    }
    
    fetch(`load-previous-jobs.php?user_id=${studentId}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#completed tbody");
            tbody.innerHTML = "";

            data.forEach(job => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${job.employer_name}</td>
                    <td>${job.job_title}</td>
                    <td>
                        <a href="review.html?job_id=${job.job_id}&employer_id=${job.employer_id}&employer_name=${encodeURIComponent(job.employer_name)}">
                            <button class="rate-btn">Rate</button>
                        </a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            console.error("Failed to load previous jobs:", err);
        });
}

function initRatingStars() {
    const urlParams = new URLSearchParams(window.location.search);
    const jobId = urlParams.get('job_id');
    const employerId = urlParams.get('employer_id');
    const employerName = urlParams.get('employer_name');

    document.querySelector("h2").textContent = `Rate ${employerName}`;

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
        if (!studentId) {
            studentId = await getStudentId();
            if (!studentId) return;
        }
        
        const comment = document.querySelector("textarea").value;

        fetch("submit-review.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                job_id: jobId,
                reviewer_id: studentId,
                reviewer_type: 'student',
                reviewee_id: employerId,
                reviewee_type: 'employer',
                rating: selectedRating,
                comment: comment
            })
        })
            .then(res => res.json())
            .then(data => {
                alert("Review submitted!");
                window.location.href = "applications.html";
            })
            .catch(err => {
                console.error("Error submitting review:", err);
                alert("Error submitting review.");
            });
    };
}

window.initRatingStars = initRatingStars;

// Single DOMContentLoaded event listener to handle all initialization
document.addEventListener("DOMContentLoaded", async () => {
    console.log('DOMContentLoaded triggered'); // Debug log
    
    // Get student ID first
    studentId = await getStudentId();
    if (!studentId) {
        console.error('Failed to get student ID');
        return;
    }
    
    console.log('Using student ID:', studentId); // Debug log
    
    // Load jobs
    loadJobs();
    
    // Load applications
    loadStudentApplications();
    
    // Load greeting
    fetch(`greeting.php?user_id=${studentId}`)
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
    
    // Handle profile form if it exists
    const nameInput = document.getElementById("full-name");
    const phoneInput = document.getElementById("phone-number");
    const emailInput = document.getElementById("email");
    const locationSelect = document.getElementById("location");
    const genderSelect = document.getElementById("gender");
    const saveBtn = document.querySelector(".save-btn");

    if (nameInput && saveBtn) {
        console.log('Fetching profile for student ID:', studentId); 
        fetch(`get-student-profile.php?id=${studentId}`)
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
                id: studentId,
                full_name: nameInput.value,
                phone: phoneInput.value,
                email: emailInput.value,
                location: locationSelect.value,
                gender: genderSelect.value
            };

            fetch("update-student-profile.php", {
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
    }
});