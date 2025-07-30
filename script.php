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
        // TODO: Load location data and populate form
        openModal('locationModal');
    }

    function editPersonnel(id) {
        // TODO: Load personnel data and populate form
        openModal('personnelModal');
    }

    function editFamily(id) {
        // TODO: Load family data and populate form
        openModal('familyModal');
    }

    function editMember(id) {
        // TODO: Load member data and populate form
        openModal('memberModal');
    }

    function editPayment(id) {
        // TODO: Load payment data and populate form
        openModal('paymentModal');
    }

    function editTeam(id) {
        // TODO: Load team data and populate form
        openModal('teamModal');
    }

    function editSession(id) {
        // TODO: Load session data and populate form
        openModal('sessionModal');
    }

    // Delete functions
    function deleteLocation(id) {
        if (confirm('Are you sure you want to delete this location?')) {
            // TODO: Send delete request to server
            location.reload();
        }
    }

    function deletePersonnel(id) {
        if (confirm('Are you sure you want to delete this personnel?')) {
            // TODO: Send delete request to server
            location.reload();
        }
    }

    function deleteFamily(id) {
        if (confirm('Are you sure you want to delete this family member?')) {
            // TODO: Send delete request to server
            location.reload();
        }
    }

    function deleteMember(id) {
        if (confirm('Are you sure you want to delete this member?')) {
            // TODO: Send delete request to server
            location.reload();
        }
    }

    function deletePayment(id) {
        if (confirm('Are you sure you want to delete this payment?')) {
            // TODO: Send delete request to server
            location.reload();
        }
    }

    function deleteTeam(id) {
        if (confirm('Are you sure you want to delete this team?')) {
            // TODO: Send delete request to server
            location.reload();
        }
    }

    function deleteSession(id) {
        if (confirm('Are you sure you want to delete this session?')) {
            // TODO: Send delete request to server
            location.reload();
        }
    }

    function generateEmails() {
        // Connect to SQL server to generate emails
        alert('Generate emails functionality - connect to SQL server');
    }

    function generateReport() {
        // Connect to SQL server to generate report
        alert('Generate report functionality - connect to SQL server');
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
    });
</script> 