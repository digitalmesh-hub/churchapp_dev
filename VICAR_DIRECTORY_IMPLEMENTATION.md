# Vicar Directory Management System - Implementation Guide

## Overview
Complete implementation of the Vicar Directory Management System with:
- Dynamic and renamable vicar positions (Main Vicar, Assistants, Supporters, etc.)
- Member assignment to vicar positions
- Backend admin interface for CRUD operations
- API endpoints for mobile/web apps

---

## Database Tables

### Tables Created:
1. **vicar_positions** - Stores position types (Main Vicar, Assistant Vicar, etc.)
2. **vicar_directory** - Assigns members to positions with date tracking

### Column Updates Made:
- `memberid` → `member_id`
- `positionid` → `vicar_position_id`
- `institutionid` → `institution_id`

### Auto Timestamps:
- `created_at` - Auto-filled on creation
- `updated_at` - Auto-updated on modification

---

## Files Created

### 1. Base Models (Auto-generated from Gii or manual)
- `/common/models/basemodels/VicarPositions.php`
- `/common/models/basemodels/VicarDirectory.php`

### 2. Extended Models (Business Logic)
- `/common/models/extendedmodels/ExtendedVicarPositions.php`
- `/common/models/extendedmodels/ExtendedVicarDirectory.php`

**Key Methods:**
- `getVicarDirectoryWithDetails($institutionId, $activeOnly)` - Get all vicars with member details
- `getMainVicar($institutionId)` - Get main vicar
- `getAssistantVicars($institutionId)` - Get all assistant vicars
- `getActivePositions($institutionId)` - Get active positions

### 3. Backend Admin Controller
- `/backend/controllers/VicardirectoryController.php`

**Actions:**
- `actionPositions()` - Manage vicar positions (list/create)
- `actionCreatePosition()` - Create new position
- `actionUpdatePosition($id)` - Update existing position
- `actionActivatePosition()` - Activate position (AJAX)
- `actionDeactivatePosition()` - Deactivate position (AJAX)
- `actionIndex()` - Manage vicar directory (list/assign)
- `actionCreateVicar()` - Assign member to position
- `actionUpdateVicar($id)` - Update vicar assignment
- `actionDeactivateVicar()` - Deactivate vicar assignment (AJAX)

### 4. Backend Views
- `/backend/views/vicardirectory/positions.php` - Manage positions
- `/backend/views/vicardirectory/index.php` - Manage vicar assignments

### 5. API v3 Controller
- `/api/modules/v3/controllers/VicardirectoryController.php`

**API Endpoints:**
- `GET /v3/vicardirectory/list-vicars` - Get complete vicar directory
- `GET /v3/vicardirectory/get-main-vicar` - Get main vicar only
- `GET /v3/vicardirectory/get-assistant-vicars` - Get assistant vicars only

---

## API Response Format

### List Vicars Response:
```json
{
  "status": 200,
  "message": "Vicar directory retrieved successfully",
  "data": {
    "positions": [
      {
        "positionId": "1",
        "positionName": "Main Vicar",
        "positionDescription": "Primary vicar of the church",
        "isMainVicar": true,
        "displayOrder": 1,
        "vicars": [
          {
            "id": "1",
            "memberId": "123",
            "membershipNo": "RM-1001",
            "title": "Rev.",
            "memberName": "John Doe",
            "firstName": "John",
            "middleName": "",
            "lastName": "Doe",
            "photoUrl": "https://example.com/photos/member123.jpg",
            "mobileNo": "+1234567890",
            "email": "john@example.com",
            "startDate": "2024-01-01",
            "endDate": null,
            "displayOrder": 0,
            "remarks": ""
          }
        ]
      },
      {
        "positionId": "2",
        "positionName": "Assistant Vicar",
        "positionDescription": "Assistant to the main vicar",
        "isMainVicar": false,
        "displayOrder": 2,
        "vicars": [
          {
            "id": "2",
            "memberId": "124",
            "membershipNo": "RM-1002",
            "title": "Mr.",
            "memberName": "Jane Smith",
            "firstName": "Jane",
            "middleName": "",
            "lastName": "Smith",
            "photoUrl": "https://example.com/photos/member124.jpg",
            "mobileNo": "+1234567891",
            "email": "jane@example.com",
            "startDate": "2024-02-01",
            "endDate": null,
            "displayOrder": 1,
            "remarks": "Youth ministry focus"
          }
        ]
      },
      {
        "positionId": "3",
        "positionName": "Youth Coordinator",
        "positionDescription": "Coordinates youth activities",
        "isMainVicar": false,
        "displayOrder": 3,
        "vicars": [
          {
            "id": "3",
            "memberId": "125",
            "title": "Mr.",
            "membershipNo": "RM-1003",
            "memberName": "Bob Johnson",
            "firstName": "Bob",
            "middleName": "",
            "lastName": "Johnson",
            "photoUrl": "https://example.com/photos/member125.jpg",
            "mobileNo": "+1234567892",
            "email": "bob@example.com",
            "startDate": "2024-03-01",
            "endDate": null,
            "displayOrder": 0,
            "remarks": ""
          }
        ]
      }
    ],
    "totalPositions": 3,
    "totalVicars": 3
  }
}
```

---

## Backend Admin Usage

### 1. Manage Positions
**URL:** `/vicardirectory/positions`

**Features:**
- Add new positions (e.g., "Main Vicar", "Youth Coordinator")
- Edit position names (fully renamable)
- Mark position as "Main Vicar" with checkbox
- Set display order for sorting
- Activate/Deactivate positions

### 2. Manage Vicar Assignments
**URL:** `/vicardirectory/index`

**Features:**
- Assign members to positions
- Select from existing members (dropdown shows: Name + Membership No)
- Set start date and optional end date
- Add remarks/notes
- View all current and historical assignments
- Edit or deactivate assignments

---

## Integration Steps

### 1. Database Setup
```bash
# Run the SQL file
mysql -u username -p database_name < create_vicar_directory_tables.sql
```

### 2. Add Menu Links (Backend)
Add to your admin navigation menu:
```php
// In backend menu configuration
[
    'label' => 'Vicar Directory',
    'items' => [
        ['label' => 'Manage Positions', 'url' => ['/vicardirectory/positions']],
        ['label' => 'Assign Vicars', 'url' => ['/vicardirectory/index']],
    ],
],
```

### 3. Configure URL Routes (API)
Add to `/api/config/_urlManager.php`:
```php
'v3/vicardirectory/list-vicars' => 'v3/vicardirectory/list-vicars',
'v3/vicardirectory/get-main-vicar' => 'v3/vicardirectory/get-main-vicar',
'v3/vicardirectory/get-assistant-vicars' => 'v3/vicardirectory/get-assistant-vicars',
```

### 4. Set Permissions (Optional)
If using RBAC, create permissions for:
- `vicar-directory-manage` - Manage positions and assignments
- `vicar-directory-view` - View vicar directory

Add to controller's `beforeAction()`:
```php
function beforeAction($action)
{   
    if (!Yii::$app->user->can('vicar-directory-manage')) {
        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
    }
    return parent::beforeAction($action);
}
```

---

## Key Features

✅ **Dynamic Positions** - Create any position type, rename anytime  
✅ **Main Vicar Tracking** - Flag for identifying the primary vicar  
✅ **Multiple Assistants** - Support for multiple people per position  
✅ **Historical Records** - Track past assignments with start/end dates  
✅ **Member Integration** - Fetches profile photos from member table  
✅ **Display Ordering** - Control display sequence  
✅ **Multi-Institution** - Works across different institutions  
✅ **Mobile API Ready** - Complete REST API with member details  
✅ **Photo URLs** - Automatic photo URL generation for API  

---

## Testing the Implementation

### Backend Testing:
1. Navigate to `/vicardirectory/positions`
2. Add a "Main Vicar" position (check "Is Main Vicar")
3. Add "Assistant Vicar" and other positions
4. Navigate to `/vicardirectory/index`
5. Assign members to positions
6. Verify display order and status

### API Testing:
```bash
# Get all vicars
curl -X GET "https://your-domain.com/api/v3/vicardirectory/list-vicars" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get main vicar only
curl -X GET "https://your-domain.com/api/v3/vicardirectory/get-main-vicar" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get assistant vicars
curl -X GET "https://your-domain.com/api/v3/vicardirectory/get-assistant-vicars" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Notes

- **Photo URLs**: The API automatically constructs photo URLs using `Yii::$app->params['image']['member']['memberImageUrl']`
- **Membership Number**: Fetched from `member.memberno` column
- **Member Names**: Concatenated from `firstName`, `middleName`, `lastName` with proper spacing (empty middle names don't create double spaces)
- **Member Title**: Fetched from the `title` table via `member.membertitle` foreign key (e.g., "Rev.", "Mr.", "Mrs.", "Dr.")
- **Active Filter**: APIs return only active assignments by default
- **Historical Data**: Deactivating doesn't delete records, sets `is_active=0` and `end_date`

---

## Troubleshooting

### Issue: Photos not showing
- Check `Yii::$app->params['image']['member']['memberImageUrl']` is configured
- Verify `memberImageThumbnail` column has values in member table

### Issue: No members in dropdown
- Ensure members exist with `active='yes'` status
- Check institution ID matches

### Issue: Position not saving
- Verify `createdby` field has valid user ID
- Check foreign key constraints

---

## Future Enhancements

- [ ] Add search/filter functionality
- [ ] Bulk assignment feature
- [ ] Email notifications on assignment
- [ ] Export vicar directory to PDF
- [ ] Photo upload directly from vicar management
- [ ] Timeline view of historical assignments

---

**Implementation completed successfully! All components are ready to use.**
