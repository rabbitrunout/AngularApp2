# AngularApp2

Angular Development Assignment 2 – Bookinsystem with Database

1- Open the booking list page
Action: Navigate to the booking list page in the application.
Expected result: The list of existing bookings is displayed without errors or layout issues.
Actual result: List displayed correctly, all bookings loaded.
Status: PASSED

2- Click "Edit" button next to a booking
Action: Click the "Edit" button on any reservation entry.
Expected result: The edit form loads, populated with the current reservation data.
Actual result: Form loaded successfully with accurate data for the selected booking.
Status: PASSED

3- Change "Location" field and click "Save"
Action: Modify the "Location" field and submit the form.
Expected result: A success message is shown, and the booking is updated in the database.
Actual result: Success notification appeared; updated data reflected on the booking list.
Status: PASSED

4- Upload a new image in the edit form
Action: Select and upload a new image file for the booking.
Expected result: New image preview displays immediately; old image file is deleted from the server.
Actual result: Preview shown; old image successfully removed from the uploads folder.
Status: PASSED

5- Submit form with empty "Location" field
Action: Clear the "Location" field and try to submit the form.
Expected result: Validation error prevents submission and informs the user of the missing field.
Actual result: Validation triggered correctly, submission blocked until field is filled.
Status: PASSED

6- Try to upload a file with .exe extension
Action: Attempt to upload an executable (.exe) file as an image.
Expected result: File upload is rejected with an error message about invalid file type.
Actual result: Upload prevented; appropriate error message displayed to user.
Status: PASSED

7- Click "Cancel" button in the edit form
Action: Click the "Cancel" button instead of saving changes.
Expected result: User is redirected back to the booking list page without any data being saved.
Actual result: Navigation back occurred with no changes saved to the reservation.
Status: PASSED

8- Check display of "Done" status
Action: View bookings marked as completed.
Expected result: Completed bookings show a green checkmark and "Done" text.
Actual result: Status displayed clearly with green styling and checkmark icon.
Status: PASSED

9- Check display of "Pending" status
Action: View bookings that are not yet completed.
Expected result: Pending bookings show yellow text with a "⏳ Pending" label.
Actual result: Pending status shown correctly with proper styling and label.
Status: PASSED

