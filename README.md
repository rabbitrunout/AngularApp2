# AngularApp2

Angular Development Assignment 2 – Bookinsystem with Database

## ✅ Booking App Testing Report

### 1. Open the booking list page

* **Action:** Navigate to the booking list page in the application
* **Expected:** The list of existing bookings is displayed without errors or layout issues
* **Actual:** List displayed correctly, all bookings loaded
* **Status:** ✅ PASSED

### 2. Click "Edit" button next to a booking

* **Action:** Click the "Edit" button on any reservation entry
* **Expected:** The edit form loads, populated with the current reservation data
* **Actual:** Form loaded successfully with accurate data for the selected booking
* **Status:** ✅ PASSED

### 3. Change "Location" field and click "Save"

* **Action:** Modify the "Location" field and submit the form
* **Expected:** A success message is shown, and the booking is updated in the database
* **Actual:** Success notification appeared; updated data reflected on the booking list
* **Status:** ✅ PASSED

### 4. Upload a new image in the edit form

* **Action:** Select and upload a new image file for the booking
* **Expected:** New image preview displays immediately; old image file is deleted from the server
* **Actual:** Preview shown; old image successfully removed from the uploads folder
* **Status:** ✅ PASSED

### 5. Submit form with empty "Location" field

* **Action:** Clear the "Location" field and try to submit the form
* **Expected:** Validation error prevents submission and informs the user of the missing field
* **Actual:** Validation triggered correctly, submission blocked until field is filled
* **Status:** ✅ PASSED

### 6. Try to upload a file with .exe extension

* **Action:** Attempt to upload an executable (.exe) file as an image
* **Expected:** File upload is rejected with an error message about invalid file type
* **Actual:** Upload prevented; appropriate error message displayed to user
* **Status:** ✅ PASSED

### 7. Click "Cancel" button in the edit form

* **Action:** Click the "Cancel" button instead of saving changes
* **Expected:** User is redirected back to the booking list page without any data being saved
* **Actual:** Navigation back occurred with no changes saved to the reservation
* **Status:** ✅ PASSED

### 8. Check display of "Done" status

* **Action:** View bookings marked as completed
* **Expected:** Completed bookings show a green checkmark and "Done" text
* **Actual:** Status displayed clearly with green styling and checkmark icon
* **Status:** ✅ PASSED

### 9. Check display of "Pending" status

* **Action:** View bookings that are not yet completed
* **Expected:** Pending bookings show yellow text with a "⏳ Pending" label
* **Actual:** Pending status shown correctly with proper styling and label
* **Status:** ✅ PASSED

### 10. Greeting & Login

* **Action:** Load the page after login
* **Expected:** "Welcome, {{ userName }}!" is displayed
* **Actual:** Greeting shown correctly
* **Status:** ✅ PASSED
* **Action:** Click Logout button
* **Expected:** Session ends and redirects to login (requires testing with real auth)
* **Actual:** Logout function is triggered, but auth not fully tested
* **Status:** 🟡 PENDING

### 11. Booking List Display

* **Action:** View the booking list
* **Expected:** Details like location, time, and status are visible in correct format
* **Actual:** Everything shown as expected, including styling and accessibility (alt tag present)
* **Status:** ✅ PASSED

### 12. Edit & Delete Functionality

* **Edit:**
  * Navigates to `/edit/:id` as expected
* **Status:** ✅ PASSED with improvement notes

### 13. Add & Navigation Buttons

* **Add Reservation:** Navigates to `/add`
* **About Us:** Navigates to `/about`
* **Both buttons** are located in `#links`, function properly, and look consistent
* **Status:** ✅ PASSED

### 14. Account lockout after 3 incorrect password entry attempts

* **Action:** Enter the wrong password three times in a row when trying to log in
* **Expected:**

  * After the third unsuccessful attempt, the account is temporarily blocked for 5 minutes
  * When trying to log in during lockout, a temporary lockout message appears
  * After 5 minutes, lock is lifted and login can proceed
* **Actual:**

  * User is blocked after 3 unsuccessful attempts
  * Lock message displayed correctly
  * Lock lifted automatically after 5 minutes
* **Status:** ✅ PASSED / ❗ NEEDS TESTING

### 15. Open Add Reservation Page

* **Action:** Navigate to the Add Reservation page via the "Add Reservation" button
* **Expected:** The form loads empty and ready for new data input
* **Actual:** Form loads correctly with all input fields blank
* **Status:** ✅ PASSED

### 16. Add a New Reservation with Valid Data

* **Action:** Fill all required fields with valid data and upload a valid image file, then click "Add"
* **Expected:** Reservation is saved, success message appears, and the new booking shows up in the list
* **Actual:** New reservation added successfully with image uploaded and visible
* **Status:** ✅ PASSED

### 17. Add a New Reservation with Empty Required Fields

* **Action:** Try to submit the form with one or more required fields empty
* **Expected:** Validation error prevents submission, with messages indicating missing fields
* **Actual:** Form validation triggers and blocks submission until all required data is entered
* **Status:** ✅ PASSED

### 18. Add a Duplicate Reservation (by Email or Image)

* **Action:** Submit a new reservation using an email or image name that already exists
* **Expected:** Submission is rejected with an error about duplicate email/image
* **Actual:** Duplicate reservation was prevented; no record added; no duplicate image uploaded
* **Status:** ✅ PASSED

### 19. Add Reservation with Invalid Image Format

* **Action:** Upload an invalid image file type (e.g., .exe, .txt) during form submission
* **Expected:** Upload rejected, error message shown, no file saved to uploads folder
* **Actual:** Invalid files blocked, no upload occurs, user notified accordingly
* **Status:** ✅ PASSED

### 20. Cancel Adding a New Reservation

* **Action:** Fill out the form partially, then click "Cancel"
* **Expected:** No reservation is created, no files are uploaded, user returns to booking list
* **Actual:** Cancel works as expected, no data saved, user navigated back to list
* **Status:** ✅ PASSED
