<script>
    function showSection(sectionId) {
        // Hide all sections
        const sections = document.querySelectorAll('.section');
        sections.forEach(section => section.classList.remove('active'));
        
        // Hide all tabs
        const tabs = document.querySelectorAll('.nav-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        
        // Show selected section
        document.getElementById(sectionId).classList.add('active');
        
        // Activate corresponding tab
        event.target.classList.add('active');
    }

    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Edit functions
    function editLocation(id) {
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_location&id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateLocationForm(data.data);
                openModal('locationModal');
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function editPersonnel(id) {
        console.log('editPersonnel called with ID:', id);
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_personnel&id=' + id
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                populatePersonnelForm(data.data);
                openModal('personnelModal');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error in editPersonnel:', error);
            alert('Error: ' + error.message);
        });
    }

    function editFamily(id) {
        console.log('editFamily called with ID:', id);
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_family&id=' + id
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                populateFamilyForm(data.data);
                openModal('familyModal');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error in editFamily:', error);
            alert('Error: ' + error.message);
        });
    }

    function editMember(id) {
        console.log('editMember called with ID:', id);
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_member&id=' + id
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                populateMemberForm(data.data);
                openModal('memberModal');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error in editMember:', error);
            alert('Error: ' + error.message);
        });
    }

    function editPayment(id) {
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_payment&id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populatePaymentForm(data.data);
                openModal('paymentModal');
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function editTeam(id) {
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_team&id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTeamForm(data.data);
                openModal('teamModal');
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function editSession(id) {
        console.log('editSession called with ID:', id);
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_session&id=' + id
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                populateSessionForm(data.data);
                openModal('sessionModal');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error in editSession:', error);
            alert('Error: ' + error.message);
        });
    }

    function editHobby(hobbyName) {
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_hobby&hobbyName=' + encodeURIComponent(hobbyName)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateHobbyForm(data.data);
                openModal('hobbyModal');
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function editWorkInfo(pID, locationID, startDate) {
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_workinfo&pID=' + pID + '&locationID=' + locationID + '&startDate=' + encodeURIComponent(startDate)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateWorkInfoForm(data.data);
                openModal('workInfoModal');
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    // New edit functions for additional tables


    // Delete functions
    function deleteLocation(id) {
        if (confirm('Are you sure you want to delete this location?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_location&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Location deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deletePersonnel(id) {
        if (confirm('Are you sure you want to delete this personnel?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_personnel&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Personnel deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deleteFamily(id) {
        if (confirm('Are you sure you want to delete this family member?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_family&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Family member deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deleteMember(id) {
        if (confirm('Are you sure you want to delete this member?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_member&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Member deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deletePayment(id) {
        if (confirm('Are you sure you want to delete this payment?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_payment&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deleteTeam(id) {
        if (confirm('Are you sure you want to delete this team?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_team&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Team deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deleteSession(id) {
        if (confirm('Are you sure you want to delete this session?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_session&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Session deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deleteHobby(hobbyName) {
        if (confirm('Are you sure you want to delete this hobby?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_hobby&hobbyName=' + encodeURIComponent(hobbyName)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Hobby deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function deleteWorkInfo(pID, locationID, startDate) {
        if (confirm('Are you sure you want to delete this work assignment?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_workinfo&pID=' + pID + '&locationID=' + locationID + '&startDate=' + encodeURIComponent(startDate)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Work assignment deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    // New delete functions for additional tables


    function removeMemberHobby(memberID, hobbyName) {
        if (confirm('Are you sure you want to remove this hobby from the member?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=remove_member_hobby&memberID=' + memberID + '&hobbyName=' + encodeURIComponent(hobbyName)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Hobby removed from member successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function viewPayment(memberID) {
        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_payment_details&memberID=' + memberID
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Create a simple modal to display payment details
                let modal = document.createElement('div');
                modal.className = 'modal';
                modal.style.display = 'block';
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
                        <h3>Payment Details</h3>
                        <div style="max-height: 400px; overflow-y: auto;">
                            ${data.data.map(payment => `
                                <div style="border: 1px solid #ddd; padding: 10px; margin: 5px 0; border-radius: 5px;">
                                    <strong>Amount:</strong> $${payment.amount}<br>
                                    <strong>Method:</strong> ${payment.paymentMethod}<br>
                                    <strong>Date:</strong> ${payment.paymentDate}<br>
                                    <strong>Type:</strong> ${payment.type}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payment details');
        });
    }

    // Form population functions
    function populateLocationForm(data) {
        document.querySelector('input[name="name"]').value = data.name || '';
        document.querySelector('select[name="type"]').value = data.type || '';
        document.querySelector('input[name="address"]').value = data.address || '';
        document.querySelector('input[name="city"]').value = data.city || '';
        document.querySelector('input[name="province"]').value = data.province || '';
        document.querySelector('input[name="postal_code"]').value = data.postalCode || '';
        document.querySelector('input[name="phone"]').value = data.phone || '';
        document.querySelector('input[name="web_address"]').value = data.webAddress || '';
        document.querySelector('input[name="max_capacity"]').value = data.maxCapacity || '';
        
        // Add hidden field for edit mode
        let hiddenField = document.querySelector('input[name="locationID"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'locationID';
            document.querySelector('#locationModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.locationID || '';
    }

    function populatePersonnelForm(data) {
        document.querySelector('input[name="first_name"]').value = data.firstName || '';
        document.querySelector('input[name="last_name"]').value = data.lastName || '';
        document.querySelector('input[name="dob"]').value = data.dob || '';
        document.querySelector('input[name="ssn"]').value = data.ssn || '';
        document.querySelector('input[name="medicare"]').value = data.medicare || '';
        document.querySelector('input[name="phone"]').value = data.phone || '';
        document.querySelector('input[name="address"]').value = data.address || '';
        document.querySelector('input[name="city"]').value = data.city || '';
        document.querySelector('input[name="province"]').value = data.province || '';
        document.querySelector('input[name="postal_code"]').value = data.postalCode || '';
        document.querySelector('input[name="email"]').value = data.email || '';
        document.querySelector('select[name="role"]').value = data.role || '';
        document.querySelector('select[name="mandate"]').value = data.mandate || '';
        document.querySelector('select[name="location_id"]').value = data.locationID || '';
        
        let hiddenField = document.querySelector('input[name="pID"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'pID';
            document.querySelector('#personnelModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.employeeID || '';
    }

    function populateFamilyForm(data) {
        document.querySelector('input[name="first_name"]').value = data.firstName || '';
        document.querySelector('input[name="last_name"]').value = data.lastName || '';
        document.querySelector('select[name="relationshipType"]').value = data.relationshipType || '';
        document.querySelector('input[name="dob"]').value = data.dob || '';
        document.querySelector('input[name="ssn"]').value = data.ssn || '';
        document.querySelector('input[name="medicare"]').value = data.medicare || '';
        document.querySelector('input[name="phone"]').value = data.phone || '';
        document.querySelector('input[name="email"]').value = data.email || '';
        document.querySelector('input[name="address"]').value = data.address || '';
        document.querySelector('input[name="city"]').value = data.city || '';
        document.querySelector('input[name="province"]').value = data.province || '';
        document.querySelector('input[name="postal_code"]').value = data.postalCode || '';
        document.querySelector('select[name="location_id"]').value = data.locationID || '';
        
        let hiddenField = document.querySelector('input[name="familyMemID"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'familyMemID';
            document.querySelector('#familyModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.familyMemID || '';
    }

    function populateMemberForm(data) {
        document.querySelector('input[name="first_name"]').value = data.firstName || '';
        document.querySelector('input[name="last_name"]').value = data.lastName || '';
        document.querySelector('input[name="dob"]').value = data.dob || '';
        document.querySelector('input[name="age"]').value = data.age || '';
        document.querySelector('input[name="height"]').value = data.height || '';
        document.querySelector('input[name="weight"]').value = data.weight || '';
        document.querySelector('input[name="ssn"]').value = data.ssn || '';
        document.querySelector('input[name="medicare"]').value = data.medicare || '';
        document.querySelector('input[name="phone"]').value = data.phone || '';
        document.querySelector('input[name="email"]').value = data.email || '';
        document.querySelector('input[name="address"]').value = data.address || '';
        document.querySelector('input[name="city"]').value = data.city || '';
        document.querySelector('input[name="province"]').value = data.province || '';
        document.querySelector('input[name="postal_code"]').value = data.postalCode || '';
        document.querySelector('select[name="location_id"]').value = data.locationID || '';
        document.querySelector('select[name="status"]').value = data.status || 'Active';
        document.querySelector('select[name="family_member_id"]').value = data.familyMemID || '';
        
        let hiddenField = document.querySelector('input[name="memberID"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'memberID';
            document.querySelector('#memberModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.memberID || '';
    }

    function populatePaymentForm(data) {
        document.querySelector('select[name="member_id"]').value = data.memberID || '';
        document.querySelector('input[name="amount"]').value = data.amount || '';
        document.querySelector('select[name="payment_method"]').value = data.method || '';
        document.querySelector('input[name="payment_date"]').value = data.paymentDate || '';
        document.querySelector('select[name="year"]').value = data.membershipYear || '';
        
        // Add hidden field for paymentID when editing
        let hiddenField = document.querySelector('input[name="paymentID"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'paymentID';
            document.querySelector('#paymentModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.paymentID || '';
    }

    function populateTeamForm(data) {
        document.querySelector('input[name="name"]').value = data.teamName || '';
        document.querySelector('select[name="gender"]').value = data.gender || '';
        document.querySelector('select[name="location_id"]').value = data.locationID || '';
        document.querySelector('select[name="head_coach_id"]').value = data.headCoachID || '';
        
        let hiddenField = document.querySelector('input[name="teamID"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'teamID';
            document.querySelector('#teamModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.teamID || '';
    }

    function populateSessionForm(data) {
        document.querySelector('select[name="type"]').value = data.sessionType || '';
        document.querySelector('input[name="date"]').value = data.sessionDate || '';
        document.querySelector('input[name="time"]').value = data.startTime || '';
        document.querySelector('input[name="address"]').value = data.address || '';
        document.querySelector('select[name="team1_id"]').value = data.team1ID || '';
        document.querySelector('select[name="team2_id"]').value = data.team2ID || '';
        
        // Combine scores if both exist
        if (data.team1Score !== null && data.team2Score !== null) {
            document.querySelector('input[name="score"]').value = data.team1Score + '-' + data.team2Score;
        } else {
            document.querySelector('input[name="score"]').value = '';
        }
        
        let hiddenField = document.querySelector('input[name="sessionID"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'sessionID';
            document.querySelector('#sessionModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.sessionID || '';
    }

    function populateHobbyForm(data) {
        document.querySelector('input[name="hobbyName"]').value = data.hobbyName || '';
        
        // Add hidden field for old hobby name when editing
        let hiddenField = document.querySelector('input[name="oldHobbyName"]');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'oldHobbyName';
            document.querySelector('#hobbyModal form').appendChild(hiddenField);
        }
        hiddenField.value = data.hobbyName || '';
    }

    function populateWorkInfoForm(data) {
        document.querySelector('select[name="pID"]').value = data.pID || '';
        document.querySelector('select[name="locationID"]').value = data.locationID || '';
        document.querySelector('input[name="startDate"]').value = data.startDate || '';
        document.querySelector('input[name="endDate"]').value = data.endDate || '';
        
        // Add hidden fields for old values when editing
        let oldPIDField = document.querySelector('input[name="oldPID"]');
        if (!oldPIDField) {
            oldPIDField = document.createElement('input');
            oldPIDField.type = 'hidden';
            oldPIDField.name = 'oldPID';
            document.querySelector('#workInfoModal form').appendChild(oldPIDField);
        }
        oldPIDField.value = data.pID || '';
        
        let oldLocationField = document.querySelector('input[name="oldLocationID"]');
        if (!oldLocationField) {
            oldLocationField = document.createElement('input');
            oldLocationField.type = 'hidden';
            oldLocationField.name = 'oldLocationID';
            document.querySelector('#workInfoModal form').appendChild(oldLocationField);
        }
        oldLocationField.value = data.locationID || '';
        
        let oldStartDateField = document.querySelector('input[name="oldStartDate"]');
        if (!oldStartDateField) {
            oldStartDateField = document.createElement('input');
            oldStartDateField.type = 'hidden';
            oldStartDateField.name = 'oldStartDate';
            document.querySelector('#workInfoModal form').appendChild(oldStartDateField);
        }
        oldStartDateField.value = data.startDate || '';
    }



    // Search functionality
    function performSearch(section) {
        const searchQuery = document.querySelector(`#${section} .search-bar`).value;
        if (searchQuery.trim() === '') {
            location.reload();
            return;
        }

        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=search&section=' + section + '&query=' + encodeURIComponent(searchQuery)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSearchResults(section, data.data);
            } else {
                alert('Search failed: ' + data.message);
            }
        });
    }

    function updateSearchResults(section, results) {
        const tbody = document.querySelector(`#${section} .data-table tbody`);
        tbody.innerHTML = '';
        
        if (results.length === 0) {
            tbody.innerHTML = '<tr><td colspan="100%" style="text-align: center;">No results found</td></tr>';
            return;
        }

        results.forEach(item => {
            const row = document.createElement('tr');
            // Generate row content based on section type
            switch (section) {
                case 'locations':
                    row.innerHTML = `
                        <td>${item.name || ''}</td>
                        <td>${item.type || ''}</td>
                        <td>${item.address || ''}</td>
                        <td>${item.maxCapacity || ''}</td>
                        <td>${item.managerID || 'N/A'}</td>
                        <td class='action-buttons'>
                            <button class='edit-btn' onclick='editLocation(${item.locationID})'>Edit</button>
                            <button class='delete-btn' onclick='deleteLocation(${item.locationID})'>Delete</button>
                        </td>
                    `;
                    break;
                case 'personnel':
                    row.innerHTML = `
                        <td>${item.firstName || ''} ${item.lastName || ''}</td>
                        <td>${item.ssn || ''}</td>
                        <td>${item.medicare || ''}</td>
                        <td>${item.role || ''}</td>
                        <td>${item.location_name || 'N/A'}</td>
                        <td>${item.startDate || 'N/A'}</td>
                        <td class='action-buttons'>
                            <button class='edit-btn' onclick='editPersonnel(${item.pID})'>Edit</button>
                            <button class='delete-btn' onclick='deletePersonnel(${item.pID})'>Delete</button>
                        </td>
                    `;
                    break;
                // Add more cases for other sections
            }
            tbody.appendChild(row);
        });
    }

    // Add search event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const searchBars = document.querySelectorAll('.search-bar');
        searchBars.forEach(searchBar => {
            searchBar.addEventListener('input', function() {
                const section = this.closest('.section').id;
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => performSearch(section), 500);
            });
        });
    });

    // Header button functions
    function showMainSystem() {
        // Hide empty template
        const emptyTemplate = document.getElementById('empty-template');
        if (emptyTemplate) {
            emptyTemplate.style.display = 'none';
        }
        
        // Show locations section by default
        const locationsSection = document.getElementById('locations');
        if (locationsSection) {
            locationsSection.style.display = 'block';
        }
        
        // Show the nav tabs
        const navTabs = document.querySelector('.nav-tabs');
        if (navTabs) {
            navTabs.style.display = 'flex';
        }
        
        // Show the content
        const content = document.querySelector('.content');
        if (content) {
            content.style.display = 'block';
        }
        
        // Update button states
        const mainBtn = document.querySelector('.btn-primary');
        const reportsBtn = document.querySelector('.btn-secondary');
        if (mainBtn) mainBtn.classList.add('active');
        if (reportsBtn) reportsBtn.classList.remove('active');
        
        // Reset navigation tab states
        const navTabButtons = document.querySelectorAll('.nav-tab');
        navTabButtons.forEach((tab, index) => {
            if (index === 0) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });
    }

    function showEmptyTemplate() {
        // Hide all main system sections
        const sections = document.querySelectorAll('.section');
        sections.forEach(section => {
            if (section.id !== 'empty-template') {
                section.style.display = 'none';
            }
        });
        
        // Show empty template
        document.getElementById('empty-template').style.display = 'block';
        
        // Hide the nav tabs completely
        const navTabs = document.querySelector('.nav-tabs');
        if (navTabs) {
            navTabs.style.display = 'none';
        }
        
        // Update button states
        const reportsBtn = document.querySelector('.btn-secondary');
        const mainBtn = document.querySelector('.btn-primary');
        if (reportsBtn) reportsBtn.classList.add('active');
        if (mainBtn) mainBtn.classList.remove('active');
    }

    function generateEmails() {
        // Generate sample emails for upcoming sessions
        const emailData = [
            {
                subject: 'Upcoming Training Session',
                body: 'Hello! This is a reminder about tomorrow\'s training session. Please arrive 15 minutes early for warm-up.',
                status: 'sent'
            },
            {
                subject: 'Game Schedule Update',
                body: 'The game scheduled for this weekend has been moved to next Saturday due to facility maintenance.',
                status: 'delivered'
            },
            {
                subject: 'Monthly Newsletter',
                body: 'Check out this month\'s newsletter with updates on team standings, upcoming events, and member highlights.',
                status: 'sent'
            }
        ];

        // Simulate email generation
        alert('Generated ' + emailData.length + ' sample emails for upcoming sessions and announcements.');
        
        // In a real implementation, this would send the emails to the database
        // For now, we'll just reload the page to show the existing emails
        location.reload();
    }

    function viewEmail(emailID) {
        // TODO: Implement email viewing modal
        alert('View email functionality - would open email details in a modal');
    }

    function deleteEmail(emailID) {
        if (confirm('Are you sure you want to delete this email?')) {
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete_email&emailID=' + emailID
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Email deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function filterMemberHobbies() {
        const memberFilter = document.getElementById('memberFilter').value;
        const rows = document.querySelectorAll('#hobbies .data-table tbody tr');
        
        rows.forEach(row => {
            if (!memberFilter || row.cells[0].textContent.includes(memberFilter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function filterWorkInfo() {
        const personnelFilter = document.getElementById('personnelFilter').value;
        const rows = document.querySelectorAll('#workinfo .data-table tbody tr');
        
        rows.forEach(row => {
            if (!personnelFilter || row.cells[0].textContent.includes(personnelFilter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // New filter functions for additional tables
    function filterLocationPhones() {
        const locationFilter = document.getElementById('locationPhoneFilter').value;
        const rows = document.querySelectorAll('#locationphones .data-table tbody tr');
        
        rows.forEach(row => {
            if (!locationFilter || row.cells[0].textContent.includes(locationFilter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function filterFamilyHistory() {
        const memberFilter = document.getElementById('familyHistoryMemberFilter').value;
        const rows = document.querySelectorAll('#familyhistory .data-table tbody tr');
        
        rows.forEach(row => {
            if (!memberFilter || row.cells[0].textContent.includes(memberFilter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function filterTeamMembers() {
        const teamFilter = document.getElementById('teamMemberFilter').value;
        const rows = document.querySelectorAll('#teammembers .data-table tbody tr');
        
        rows.forEach(row => {
            if (!teamFilter || row.cells[0].textContent.includes(teamFilter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function generateReport() {
        const reportType = document.getElementById('reportType').value;
        const locationFilter = document.querySelector('#reports select[onchange="generateReport()"] + select')?.value || '';
        const defaultTable = document.getElementById('defaultReportTable');
        const yearlyPaymentsReport = document.getElementById('yearlyPaymentsReport');
        
        // Hide all report sections
        if (defaultTable) defaultTable.style.display = 'none';
        if (yearlyPaymentsReport) yearlyPaymentsReport.style.display = 'none';
        
        // Show appropriate report
        if (reportType === 'yearly_payments') {
            if (yearlyPaymentsReport) yearlyPaymentsReport.style.display = 'block';
        } else {
            // Generate dynamic report
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=generate_report&reportType=' + reportType + '&locationFilter=' + locationFilter
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateReportResults(reportType, data.data);
                } else {
                    alert('Report generation failed: ' + data.message);
                }
            });
        }
    }

    function updateReportResults(reportType, data) {
        const defaultTable = document.getElementById('defaultReportTable');
        const tbody = defaultTable.querySelector('tbody');
        tbody.innerHTML = '';
        
        if (defaultTable) defaultTable.style.display = 'table';
        
        if (Array.isArray(data)) {
            // Handle array results
            data.forEach(item => {
                const row = document.createElement('tr');
                switch (reportType) {
                    case 'locations':
                        row.innerHTML = `
                            <td>${item.name || ''}</td>
                            <td>${item.maxCapacity || ''}</td>
                            <td>${item.member_count || '0'}</td>
                        `;
                        break;
                    case 'inactive':
                        row.innerHTML = `
                            <td>${item.firstName || ''} ${item.lastName || ''}</td>
                            <td>${item.dob || ''}</td>
                            <td>${item.location_name || 'N/A'}</td>
                        `;
                        break;
                    default:
                        row.innerHTML = `<td>${JSON.stringify(item)}</td><td>Data</td>`;
                }
                tbody.appendChild(row);
            });
        } else {
            // Handle single object results
            const row = document.createElement('tr');
            switch (reportType) {
                case 'members':
                    row.innerHTML = `
                        <td>Total Members</td>
                        <td>${data.total_members || '0'}</td>
                    `;
                    tbody.appendChild(row);
                    
                    const minorsRow = document.createElement('tr');
                    minorsRow.innerHTML = `
                        <td>Minors (< 18)</td>
                        <td>${data.minors || '0'}</td>
                    `;
                    tbody.appendChild(minorsRow);
                    
                    const adultsRow = document.createElement('tr');
                    adultsRow.innerHTML = `
                        <td>Adults (18+)</td>
                        <td>${data.adults || '0'}</td>
                    `;
                    tbody.appendChild(adultsRow);
                    break;
                default:
                    row.innerHTML = `<td>${JSON.stringify(data)}</td><td>Data</td>`;
                    tbody.appendChild(row);
            }
        }
    }

    function exportReport() {
        // Export report functionality
        alert('Export report functionality - connect to SQL server');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Handle member DOB change to show/hide family section
        const dobInput = document.querySelector('input[name="dob"]');
        if (dobInput) {
            dobInput.addEventListener('change', function() {
                const birthDate = new Date(this.value);
                const today = new Date();
                const age = today.getFullYear() - birthDate.getFullYear();
                
                const minorSection = document.getElementById('minorSection');
                if (minorSection) {
                    if (age < 18) {
                        minorSection.style.display = 'block';
                    } else {
                        minorSection.style.display = 'none';
                    }
                }
            });
        }

        // Handle session type change
        const sessionTypeSelect = document.querySelector('select[name="type"]');
        if (sessionTypeSelect) {
            sessionTypeSelect.addEventListener('change', function() {
                const team2Group = document.getElementById('team2Group');
                const scoreGroup = document.getElementById('scoreGroup');
                
                if (this.value === 'training') {
                    if (team2Group) team2Group.style.display = 'none';
                    if (scoreGroup) scoreGroup.style.display = 'none';
                } else if (this.value === 'game') {
                    if (team2Group) team2Group.style.display = 'block';
                    if (scoreGroup) scoreGroup.style.display = 'block';
                }
            });
        }

        // Handle family type change
        const familyTypeSelect = document.querySelector('select[name="type"]');
        if (familyTypeSelect) {
            familyTypeSelect.addEventListener('change', function() {
                const secondarySection = document.getElementById('secondaryFamilySection');
                if (secondarySection) {
                    if (this.value === 'Primary') {
                        secondarySection.style.display = 'block';
                    } else {
                        secondarySection.style.display = 'none';
                    }
                }
            });
        }

        // Handle session filtering
        const dateFromInput = document.getElementById('dateFrom');
        const dateToInput = document.getElementById('dateTo');
        const sessionTypeFilter = document.getElementById('sessionTypeFilter');
        const locationFilter = document.getElementById('locationFilter');
        
        if (dateFromInput && dateToInput) {
            dateFromInput.addEventListener('change', filterSessions);
            dateToInput.addEventListener('change', filterSessions);
        }
        
        if (sessionTypeFilter) {
            sessionTypeFilter.addEventListener('change', filterSessions);
        }
        
        if (locationFilter) {
            locationFilter.addEventListener('change', filterSessions);
        }
    });

    // Function to filter sessions by all criteria
    function filterSessions() {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const sessionType = document.getElementById('sessionTypeFilter').value;
        const location = document.getElementById('locationFilter').value;
        const rows = document.querySelectorAll('#sessions .data-table tbody tr');
        
        rows.forEach(row => {
            const typeCell = row.cells[0]; // Type column
            const dateTimeCell = row.cells[1]; // Date & Time column
            const locationCell = row.cells[2]; // Location column
            
            let showRow = true;
            
            // Filter by session type
            if (sessionType && typeCell && typeCell.textContent.toLowerCase() !== sessionType.toLowerCase()) {
                showRow = false;
            }
            
            // Filter by date range
            if (dateTimeCell) {
                const sessionDate = dateTimeCell.textContent.split(' ')[0]; // Extract date part
                
                if (dateFrom && sessionDate < dateFrom) {
                    showRow = false;
                }
                
                if (dateTo && sessionDate > dateTo) {
                    showRow = false;
                }
            }
            
            // Filter by location
            if (location && locationCell && locationCell.textContent.toLowerCase() !== location.toLowerCase()) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
</script>