<!-- Location Modal -->
<div id="locationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('locationModal')">&times;</span>
        <h3>Add/Edit Location</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_location">
            <div class="form-grid">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Type:</label>
                    <select name="type" required>
                        <option value="">Select Type</option>
                        <option value="Head">Head</option>
                        <option value="Branch">Branch</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <input type="text" name="address" required>
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>Province:</label>
                    <input type="text" name="province" required>
                </div>
                <div class="form-group">
                    <label>Postal Code:</label>
                    <input type="text" name="postal_code" required>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="tel" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Web Address:</label>
                    <input type="url" name="web_address">
                </div>
                <div class="form-group">
                    <label>Maximum Capacity:</label>
                    <input type="number" name="max_capacity" required>
                </div>
            </div>
            <button type="submit" class="btn">Save Location</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('locationModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Personnel Modal -->
<div id="personnelModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('personnelModal')">&times;</span>
        <h3>Add/Edit Personnel</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_personnel">
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <input type="date" name="dob" required>
                </div>
                <div class="form-group">
                    <label>Social Security Number:</label>
                    <input type="text" name="ssn" placeholder="XXX-XX-XXXX" required>
                </div>
                <div class="form-group">
                    <label>Medicare Card Number:</label>
                    <input type="text" name="medicare" required>
                </div>
                <div class="form-group">
                    <label>Telephone Number:</label>
                    <input type="tel" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <input type="text" name="address" required>
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>Province:</label>
                    <input type="text" name="province" required>
                </div>
                <div class="form-group">
                    <label>Postal Code:</label>
                    <input type="text" name="postal_code" required>
                </div>
                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Role:</label>
                    <select name="role" required>
                        <option value="">Select Role</option>
                        <option value="General Manager">General Manager</option>
                        <option value="Deputy Manager">Deputy Manager</option>
                        <option value="Treasurer">Treasurer</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Captain">Captain</option>
                        <option value="Coach">Coach</option>
                        <option value="Assistant Coach">Assistant Coach</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mandate:</label>
                    <select name="mandate" required>
                        <option value="">Select Mandate</option>
                        <option value="Volunteer">Volunteer</option>
                        <option value="Salaried">Salaried</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <select name="location_id" required>
                        <option value="">Select Location</option>
                        <?php
                        $locations = getLocations($pdo);
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Date:</label>
                    <input type="date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label>End Date:</label>
                    <input type="date" name="end_date">
                    <small>Leave blank if still active</small>
                </div>
            </div>
            <button type="submit" class="btn">Save Personnel</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('personnelModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Family Modal -->
<div id="familyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('familyModal')">&times;</span>
        <h3>Add/Edit Family Member</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_family">
            <div class="form-grid">
                <div class="form-group">
                    <label>Relationship Type:</label>
                    <select name="relationshipType" required>
                        <option value="">Select Relationship</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Grandfather">Grandfather</option>
                        <option value="Grandmother">Grandmother</option>
                        <option value="Tutor">Tutor</option>
                        <option value="Partner">Partner</option>
                        <option value="Friend">Friend</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <input type="date" name="dob" required>
                </div>
                <div class="form-group">
                    <label>Social Security Number:</label>
                    <input type="text" name="ssn" required>
                </div>
                <div class="form-group">
                    <label>Medicare Card Number:</label>
                    <input type="text" name="medicare" required>
                </div>
                <div class="form-group">
                    <label>Telephone Number:</label>
                    <input type="tel" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <input type="text" name="address" required>
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>Province:</label>
                    <input type="text" name="province" required>
                </div>
                <div class="form-group">
                    <label>Postal Code:</label>
                    <input type="text" name="postal_code" required>
                </div>
                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <select name="location_id" required>
                        <option value="">Select Location</option>
                        <?php
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn">Save Family Member</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('familyModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Member Modal -->
<div id="memberModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('memberModal')">&times;</span>
        <h3>Add/Edit Member</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_member">
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <input type="date" name="dob" required>
                </div>
                <div class="form-group">
                    <label>Age:</label>
                    <input type="number" name="age" required>
                </div>
                <div class="form-group">
                    <label>Height (cm):</label>
                    <input type="number" name="height" step="0.01">
                </div>
                <div class="form-group">
                    <label>Weight (kg):</label>
                    <input type="number" name="weight" step="0.01">
                </div>
                <div class="form-group">
                    <label>Social Security Number:</label>
                    <input type="text" name="ssn" required placeholder="XXX-XX-XXXX">
                </div>
                <div class="form-group">
                    <label>Medicare Card Number:</label>
                    <input type="text" name="medicare">
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <select name="location_id" required>
                        <option value="">Select Location</option>
                        <?php
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="tel" name="phone">
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <input type="text" name="address" required>
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>Province:</label>
                    <input type="text" name="province" required>
                </div>
                <div class="form-group">
                    <label>Postal Code:</label>
                    <input type="text" name="postal_code" required>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Suspended">Suspended</option>
                    </select>
                </div>
            </div>
            <!-- For Minor Members -->
            <div id="minorSection" style="display: none; border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px;">
                <h4>Family Information (For Minors)</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Family Member:</label>
                        <select name="family_member_id">
                            <option value="">Select Family Member</option>
                            <?php
                            $familyMembers = getFamilyMembers($pdo);
                            foreach ($familyMembers as $family) {
                                echo "<option value='" . $family['familyMemID'] . "'>" . htmlspecialchars($family['firstName'] . ' ' . $family['lastName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Relationship:</label>
                        <select name="relationship">
                            <option value="">Select Relationship</option>
                            <option value="parent">Parent</option>
                            <option value="guardian">Guardian</option>
                            <option value="sibling">Sibling</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn">Save Member</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('memberModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('paymentModal')">&times;</span>
        <h3>Record Payment</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_payment">
            <div class="form-grid">
                <div class="form-group">
                    <label>Member:</label>
                    <select name="member_id" required>
                        <option value="">Select Member</option>
                        <?php
                        $members = getMembers($pdo);
                        foreach ($members as $member) {
                            echo "<option value='" . $member['memberID'] . "'>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" name="amount" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Payment Method:</label>
                    <select name="payment_method" required>
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="credit">Credit Card</option>
                        <option value="debit">Debit Card</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Date:</label>
                    <input type="date" name="payment_date" required>
                </div>
                <div class="form-group">
                    <label>Year:</label>
                    <select name="year" required>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Type:</label>
                    <select name="type" required>
                        <option value="membership">Membership</option>
                        <option value="donation">Donation</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn">Record Payment</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('paymentModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Team Modal -->
<div id="teamModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('teamModal')">&times;</span>
        <h3>Create/Edit Team</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_team">
            <div class="form-grid">
                <div class="form-group">
                    <label>Team Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Head Coach:</label>
                    <select name="head_coach_id" required>
                        <option value="">Select Coach</option>
                        <?php
                        $coaches = $pdo->query("SELECT p.employeeID, per.firstName, per.lastName 
                                               FROM Personnel p 
                                               LEFT JOIN Person per ON p.employeeID = per.pID 
                                               WHERE p.role IN ('Coach', 'AssistantCoach')")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($coaches as $coach) {
                            echo "<option value='" . $coach['employeeID'] . "'>" . htmlspecialchars($coach['firstName'] . ' ' . $coach['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <select name="location_id" required>
                        <option value="">Select Location</option>
                        <?php
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <h4>Team Players</h4>
            <div class="form-group">
                <label>Add Players:</label>
                <select name="players[]" multiple style="height: 120px;">
                    <?php
                    foreach ($members as $member) {
                        echo "<option value='" . $member['memberID'] . "'>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                    }
                    ?>
                </select>
                <small>Hold Ctrl/Cmd to select multiple players</small>
            </div>
            <button type="submit" class="btn">Save Team</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('teamModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Session Modal -->
<div id="sessionModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('sessionModal')">&times;</span>
        <h3>Schedule Session</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_session">
            <div class="form-grid">
                <div class="form-group">
                    <label>Session Type:</label>
                    <select name="type" required>
                        <option value="">Select Type</option>
                        <option value="game">Game</option>
                        <option value="training">Training</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date:</label>
                    <input type="date" name="date" required>
                </div>
                <div class="form-group">
                    <label>Time:</label>
                    <input type="time" name="time" required>
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <select name="location_id" required>
                        <option value="">Select Location</option>
                        <?php
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Team 1:</label>
                    <select name="team1_id" required>
                        <option value="">Select Team</option>
                        <?php
                        $teams = getTeams($pdo);
                        foreach ($teams as $team) {
                            echo "<option value='" . $team['teamID'] . "'>" . htmlspecialchars($team['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" id="team2Group">
                    <label>Team 2 (for games):</label>
                    <select name="team2_id">
                        <option value="">Select Team</option>
                        <?php
                        foreach ($teams as $team) {
                            echo "<option value='" . $team['teamID'] . "'>" . htmlspecialchars($team['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Head Coach:</label>
                    <select name="coach_id" required>
                        <option value="">Select Coach</option>
                        <?php
                        foreach ($coaches as $coach) {
                            echo "<option value='" . $coach['employeeID'] . "'>" . htmlspecialchars($coach['firstName'] . ' ' . $coach['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" id="scoreGroup" style="display: none;">
                    <label>Score (Team1-Team2):</label>
                    <input type="text" name="score" placeholder="e.g., 3-1">
                </div>
            </div>
            <button type="submit" class="btn">Schedule Session</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('sessionModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Hobby Modal -->
<div id="hobbyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('hobbyModal')">&times;</span>
        <h3>Add New Hobby</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_hobby">
            <div class="form-grid">
                <div class="form-group">
                    <label>Hobby Name:</label>
                    <input type="text" name="hobbyName" required placeholder="e.g., Swimming, Reading, Gaming">
                </div>
            </div>
            <button type="submit" class="btn">Save Hobby</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('hobbyModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Member Hobby Modal -->
<div id="memberHobbyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('memberHobbyModal')">&times;</span>
        <h3>Assign Hobby to Member</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_member_hobby">
            <div class="form-grid">
                <div class="form-group">
                    <label>Member:</label>
                    <select name="memberID" required>
                        <option value="">Select Member</option>
                        <?php
                        $members = getMembers($pdo);
                        foreach ($members as $member) {
                            echo "<option value='" . $member['memberID'] . "'>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Hobby:</label>
                    <select name="hobbyName" required>
                        <option value="">Select Hobby</option>
                        <?php
                        $hobbies = getHobbies($pdo);
                        foreach ($hobbies as $hobby) {
                            echo "<option value='" . htmlspecialchars($hobby['hobbyName']) . "'>" . htmlspecialchars($hobby['hobbyName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn">Assign Hobby</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('memberHobbyModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Work Info Modal -->
<div id="workInfoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('workInfoModal')">&times;</span>
        <h3>Add Work Assignment</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_workinfo">
            <div class="form-grid">
                <div class="form-group">
                    <label>Personnel:</label>
                    <select name="employeeID" required>
                        <option value="">Select Personnel</option>
                        <?php
                        $personnel = getPersonnel($pdo);
                        foreach ($personnel as $person) {
                            echo "<option value='" . $person['employeeID'] . "'>" . htmlspecialchars($person['firstName'] . ' ' . $person['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <select name="locationID" required>
                        <option value="">Select Location</option>
                        <?php
                        $locations = getLocations($pdo);
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Date:</label>
                    <input type="date" name="startDate" required>
                </div>
                <div class="form-group">
                    <label>End Date (Optional):</label>
                    <input type="date" name="endDate">
                    <small>Leave empty if assignment is ongoing</small>
                </div>
            </div>
            <button type="submit" class="btn">Save Work Assignment</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('workInfoModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Postal Area Modal -->
<div id="postalAreaModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('postalAreaModal')">&times;</span>
        <h3>Add/Edit Postal Area</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_postalarea">
            <div class="form-grid">
                <div class="form-group">
                    <label>Postal Code:</label>
                    <input type="text" name="postalCode" required placeholder="e.g., H3A 1A1">
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>Province:</label>
                    <input type="text" name="province" required>
                </div>
            </div>
            <button type="submit" class="btn">Save Postal Area</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('postalAreaModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Location Phone Modal -->
<div id="locationPhoneModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('locationPhoneModal')">&times;</span>
        <h3>Add/Edit Location Phone</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_locationphone">
            <div class="form-grid">
                <div class="form-group">
                    <label>Location:</label>
                    <select name="locationID" required>
                        <option value="">Select Location</option>
                        <?php
                        $locations = getLocations($pdo);
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="tel" name="phone" required placeholder="e.g., (514) 555-1234">
                </div>
            </div>
            <button type="submit" class="btn">Save Location Phone</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('locationPhoneModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Family History Modal -->
<div id="familyHistoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('familyHistoryModal')">&times;</span>
        <h3>Add/Edit Family History</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_familyhistory">
            <div class="form-grid">
                <div class="form-group">
                    <label>Member:</label>
                    <select name="memberID" required>
                        <option value="">Select Member</option>
                        <?php
                        $members = getMembers($pdo);
                        foreach ($members as $member) {
                            echo "<option value='" . $member['memberID'] . "'>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Family Member:</label>
                    <select name="familyMemID" required>
                        <option value="">Select Family Member</option>
                        <?php
                        $familyMembers = getFamilyMembers($pdo);
                        foreach ($familyMembers as $family) {
                            echo "<option value='" . $family['familyMemID'] . "'>" . htmlspecialchars($family['firstName'] . ' ' . $family['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Type:</label>
                    <select name="type" required>
                        <option value="">Select Type</option>
                        <option value="Primary">Primary</option>
                        <option value="Secondary">Secondary</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Date:</label>
                    <input type="date" name="startDate" required>
                </div>
                <div class="form-group">
                    <label>End Date (Optional):</label>
                    <input type="date" name="endDate">
                    <small>Leave empty if relationship is ongoing</small>
                </div>
            </div>
            <button type="submit" class="btn">Save Family History</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('familyHistoryModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Team Member Modal -->
<div id="teamMemberModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('teamMemberModal')">&times;</span>
        <h3>Add/Edit Team Member</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_teammember">
            <div class="form-grid">
                <div class="form-group">
                    <label>Team:</label>
                    <select name="teamID" required>
                        <option value="">Select Team</option>
                        <?php
                        $teams = getTeams($pdo);
                        foreach ($teams as $team) {
                            echo "<option value='" . $team['teamID'] . "'>" . htmlspecialchars($team['teamName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Member:</label>
                    <select name="memberID" required>
                        <option value="">Select Member</option>
                        <?php
                        $members = getMembers($pdo);
                        foreach ($members as $member) {
                            echo "<option value='" . $member['memberID'] . "'>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Role in Team:</label>
                    <select name="roleInTeam" required>
                        <option value="">Select Role</option>
                        <option value="Goalkeeper">Goalkeeper</option>
                        <option value="Defender">Defender</option>
                        <option value="Midfielder">Midfielder</option>
                        <option value="Forward">Forward</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn">Save Team Member</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('teamMemberModal')">Cancel</button>
        </form>
    </div>
</div> 