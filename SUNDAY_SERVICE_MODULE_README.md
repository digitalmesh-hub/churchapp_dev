# Sunday Service Module Implementation

This document outlines the implementation of the Sunday Service module for the institution management system.

## Overview

The Sunday Service module allows administrators to manage Sunday service content with WYSIWYG HTML editing capabilities, date management (preventing past dates), and active/inactive status control. The module includes both backend admin functionality and API endpoints.

**Institution-Level Feature Control**: This module includes environment-based feature flags that allow selective enablement for specific institutions via the `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable. Only institutions listed in this variable will have access to Sunday Service functionality.

## Database Setup

### Table Creation

Run the following SQL script to create the required table:

```sql
-- File: create_sunday_service_table.sql
CREATE TABLE IF NOT EXISTS `sunday_service` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `service_date` DATE NOT NULL COMMENT 'Date of the Sunday service',
  `content` TEXT NOT NULL COMMENT 'HTML content for the service',
  `institution_id` INT(11) NULL,
  `active` INT(4) DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_by` INT(11) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` INT(11) NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx-sunday_service-institution_id` (`institution_id`),
  KEY `idx-sunday_service-service_date` (`service_date`),
  KEY `idx-sunday_service-active` (`active`),
  CONSTRAINT `fk-sunday_service-institution_id` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-sunday_service-created_by` FOREIGN KEY (`created_by`) REFERENCES `usercredentials` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-sunday_service-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `usercredentials` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Files Created

### 1. Database Schema
- `create_sunday_service_table.sql` - SQL table creation script

### 2. Models
- `common/models/basemodels/SundayService.php` - Base model with table definition and relations
- `common/models/extendedmodels/ExtendedSundayService.php` - Extended model with business logic and validation
- `common/models/searchmodels/SundayServiceSearch.php` - Search model for server-side pagination

### 3. Backend Controller
- `backend/controllers/SundayServiceController.php` - CRUD operations with server-side pagination and institution feature flag check

### 4. Backend Views
- `backend/views/sunday-service/index.php` - List view with search and pagination
- `backend/views/sunday-service/_form.php` - Form with WYSIWYG editor
- `backend/views/sunday-service/create.php` - Create view
- `backend/views/sunday-service/update.php` - Update view
- `backend/views/sunday-service/view.php` - Detail view

### 5. Backend Layout (Modified)
- `backend/views/layouts/AdminMain.php` - Added Sunday Service menu item with permission and feature flag check

### 6. API Controller
- `api/modules/v3/controllers/SundayServiceController.php` - API endpoint with pagination support and institution feature flag check

### 7. Helper Class
- `common/helpers/UserHelper.php` - User display name utility (reusable across the application)

## Features

### Backend Admin Features

1. **Date Field**
   - Uses Kartik DatePicker widget
   - Prevents selection of past dates (startDate: '0d')
   - Auto-validation to ensure service dates are not in the past

2. **WYSIWYG HTML Editor**
   - Uses Summernote editor (same as News module)
   - Supports rich text formatting, links, tables, etc.
   - Image upload is disabled (plain HTML editor only)

3. **Active/Inactive Status**
   - Dropdown to set status (similar to Zone module)
   - Default value: Active (1)

4. **Server-Side Pagination**
   - GridView with pagination (1 item per page for testing)
   - Date range filters using Kartik DatePicker
     - Service Date Range: From date and To date fields
     - Created Date Range: From date and To date fields
   - Sortable columns
   - Date format: 'dd MM yyyy' (e.g., "12 March 2026")
   - Calendar popup for easy date selection

5. **Timestamps**
   - `created_at` and `updated_at` fields auto-populated by database
   - Uses standard TIMESTAMP fields with default values

6. **Institution-Level Feature Control**
   - Environment variable controls which institutions can access Sunday Service
   - Backend throws 403 Forbidden exception for unauthorized institutions
   - Menu item only appears for enabled institutions
   - Configurable via `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable

### API Features

#### Endpoint: `GET /api/v3/sunday-service/list-sunday-services`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per-page` (optional): Items per page (default: 10)
- `all` (optional): Set to 'true' or '1' to get all records without pagination

**Authentication:**
- Requires valid session token

**Response Format (Paginated):**
```json
{
  "status": 200,
  "message": "Sunday services retrieved successfully",
  "data": {
    "sundayServices": [
      {
        "id": 1,
        "serviceDate": "25 December 2024",
        "serviceDateRaw": "2024-12-25",
        "content": "<p>Service content in HTML...</p>",
        "active": 1,
        "createdAt": "20 March 2024"
      }
    ],
    "pagination": {
      "totalCount": 50,
      "pageCount": 5,
      "currentPage": 1,
      "perPage": 10
    }
  }
}
```

**Response Format (All Records):**
```json
{
  "status": 200,
  "message": "Sunday services retrieved successfully",
  "data": {
    "sundayServices": [...],
    "totalCount": 50,
    "pagination": false
  }
}
```

**Error Response (Session Invalid):**
```json
{
  "status": 498,
  "message": "Session invalid",
  "data": {}
}
```

**Error Response (Feature Not Enabled):**
```json
{
  "status": 403,
  "message": "Sunday Service feature is not enabled for this institution",
  "data": {}
}
```

**Error Response (No Records):**
```json
{
  "status": 200,
  "message": "No Sunday services found",
  "data": {
    "sundayServices": [],
    "pagination": {...}
  }
}
```

## Usage Examples

### Backend Access

**Prerequisites:**
- User must be authenticated
- User must have the required permission (`b46fb1de-ec46-11e6-b48e-000c2990e707`)
- Institution ID must be in `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable

1. **List all Sunday Services:**
   - Navigate to: `/backend/sunday-service/index`
   - Use date range filters to filter by:
     - Service Date Range: Select From date and To date
     - Created Date Range: Select From date and To date
   - Click "Filter" to apply filters
   - Pagination is set to 1 item per page for testing

2. **Create new Sunday Service:**
   - Navigate to: `/backend/sunday-service/create`
   - Fill in the service date (only future/current dates allowed)
   - Add content using the WYSIWYG editor
   - Select active/inactive status
   - Click "Create"

3. **Update Sunday Service:**
   - From the list, click the pencil icon
   - Modify fields as needed
   - Click "Update"

### API Access

**Prerequisites:**
- Valid session token required
- Institution ID must be in `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable

1. **Get paginated list (10 per page):**
   ```
   GET /api/v3/sunday-service/list-sunday-services?page=1&per-page=10
   ```

2. **Get all active records:**
   ```
   GET /api/v3/sunday-service/list-sunday-services?all=true
   ```

3. **Get custom page size:**
   ```
   GET /api/v3/sunday-service/list-sunday-services?page=2&per-page=20
   ```

## Access Control

### Backend
The controller uses `AccessControl` behavior. Update the role permissions in:
- `backend/controllers/SundayServiceController.php`

Current setup allows authenticated users (`@`). You may want to replace with specific role IDs like:
```php
'roles' => ['893232ae-ec46-11e6-b48e-000c2990e707'] // Example role ID
```

**Institution-Level Feature Control:**
The backend controller checks the `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable in the `beforeAction()` method. If the current institution is not in the enabled list, a `403 Forbidden` HTTP exception is thrown.

### API
The API inherits authentication from `BaseController`. Ensure users are authenticated before accessing the endpoint.

**Institution-Level Feature Control:**
The API controller checks the `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable. If the current institution is not in the enabled list, a `403 Forbidden` response is returned.

### Menu Visibility
The Sunday Service menu item in `backend/views/layouts/AdminMain.php` only appears if:
1. User has the required permission (`b46fb1de-ec46-11e6-b48e-000c2990e707`)
2. Institution ID is in the `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable

## Environment Configuration

### Required Environment Variable

Add this to your `.env` file to enable Sunday Service for specific institutions:

```bash
# Enable Sunday Service for specific institutions (comma-separated IDs)
SUNDAY_SERVICE_ENABLED_INSTITUTIONS=1,5,12,45
```

**Configuration Details:**
- **Variable Name**: `SUNDAY_SERVICE_ENABLED_INSTITUTIONS`
- **Format**: Comma-separated list of institution IDs (no spaces)
- **Example**: `1,5,12,45` enables for institutions 1, 5, 12, and 45
- **Empty/Not Set**: No institutions have access to Sunday Service
- **Behavior**: Only listed institutions can access backend and API endpoints

**Security & Access:**
- Backend: Throws `403 Forbidden` exception if institution not enabled
- API: Returns `403` status code with error message
- Menu: Sunday Service menu item hidden if institution not enabled

## Data Validation

1. **Required Fields:**
   - Service Date
   - Content
   - Institution ID (required on create, but nullable in DB for deletion handling)
   - Created By (set automatically by controller)

2. **Optional Fields:**
   - Updated By (nullable)
   - Service date cannot be in the past
   - Content must be a string
   - Active status: 0 or 1

## UserHelper Utility

The module includes a reusable helper class for displaying user names throughout the application.

### Location
`common/helpers/UserHelper.php`

### Method
```php
UserHelper::getUserDisplayName($userId)
```

### Purpose
Resolves a user ID to a human-readable display name by checking multiple data sources in order:
1. If the user is a member - uses member name fields (firstName, middleName, lastName)
2. If the user is a spouse - uses spouse name fields (spouse_firstName, spouse_middleName, spouse_lastName)
3. If the user has a profile - uses userprofile name fields (firstname, middlename, lastname)
4. Falls back to emailid if no name is found
5. Returns 'N/A' if user ID is null or user not found

### Usage Examples

**In Views:**
```php
use common\helpers\UserHelper;

// Display created by user name
<?= UserHelper::getUserDisplayName($model->created_by) ?>

// In DetailView attributes
[
    'attribute' => 'created_by',
    'value' => UserHelper::getUserDisplayName($model->created_by),
]
```

**In Controllers or Models:**
```php
use common\helpers\UserHelper;

$userName = UserHelper::getUserDisplayName($userId);
```

This helper eliminates code duplication and provides a consistent way to display user names across the entire application.


## Notes

- **Environment Variable Required**: `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` must be set in `.env` file with comma-separated institution IDs for feature access
- The module follows the same patterns as the News and Zone modules
- WYSIWYG editor uses Summernote (already included in the project)
- Image upload is disabled in the editor
- All dates are handled according to the institution's timezone
- Timestamps (`created_at`, `updated_at`) are auto-populated by database
- Soft delete is not implemented; records are permanently deleted
- Only institutions listed in `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` can access the feature
- Menu item is hidden for institutions without access
- Backend throws 403 Forbidden for unauthorized access attempts
- API returns 403 status for unauthorized institutions

## Testing Checklist

- [ ] Set up `SUNDAY_SERVICE_ENABLED_INSTITUTIONS` environment variable
- [ ] Verify menu item appears only for enabled institutions
- [ ] Test 403 Forbidden response for non-enabled institutions (backend)
- [ ] Test 403 Forbidden response for non-enabled institutions (API)
- [ ] Create Sunday Service with future date
- [ ] Try to create with past date (should fail validation)
- [ ] Update existing service
- [ ] Delete service
- [ ] Test WYSIWYG editor
- [ ] Test pagination in backend
- [ ] Test API with pagination parameters
- [ ] Test API with 'all' parameter
- [ ] Verify only active services appear in API
- [ ] Test search functionality
- [ ] Test date range filters

## Future Enhancements

Consider implementing:
- Soft delete functionality
- Draft/publish workflow
- Multi-language support
- Scheduled publishing
- Email notifications to members
- PDF export of services
