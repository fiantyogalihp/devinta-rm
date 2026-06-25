# Pagination Implementation Plan - Hospital RM Search

## Overview
Add pagination functionality to the patient list in dashboard.php to improve performance and user experience when dealing with large datasets.

## Requirements

### Functional Requirements
1. Display limited number of records per page (default: 20)
2. Allow users to change records per page (10, 20, 50, 100)
3. Show current page and total pages information
4. Provide navigation controls (First, Previous, Next, Last)
5. Display page numbers with ellipsis for large page counts
6. Maintain search parameters across page navigation
7. Show total records count

### Non-Functional Requirements
1. Fast query performance with LIMIT/OFFSET
2. Responsive pagination controls for mobile
3. Accessible navigation controls
4. SEO-friendly URL structure

## Technical Design

### 1. Database Query Modification

**Current Query:**
```php
$sql = "SELECT * FROM patients WHERE ... ORDER BY created_at DESC";
```

**New Query with Pagination:**
```php
// Count total records
$countSql = "SELECT COUNT(*) as total FROM patients WHERE ...";

// Get paginated records
$sql = "SELECT * FROM patients WHERE ... ORDER BY created_at DESC LIMIT ? OFFSET ?";
```

### 2. Pagination Logic

**Variables:**
```php
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$offset = ($page - 1) * $perPage;
$totalRecords = 0; // from COUNT query
$totalPages = ceil($totalRecords / $perPage);
```

**Validation:**
- Page must be >= 1
- Per page must be in allowed values (10, 20, 50, 100)
- If page > totalPages, redirect to last page

### 3. URL Parameter Management

**URL Structure:**
```
dashboard.php?q=search_term&type=nama&page=2&per_page=20
```

**Helper Function:**
```php
function buildPaginationUrl($page, $perPage = null) {
    $params = $_GET;
    $params['page'] = $page;
    if ($perPage !== null) {
        $params['per_page'] = $perPage;
    }
    return 'dashboard.php?' . http_build_query($params);
}
```

### 4. Pagination UI Components

#### A. Records Per Page Selector
```html
<div class="pagination-controls">
    <label>Show per page:</label>
    <select onchange="location.href=this.value">
        <option value="?...&per_page=10">10</option>
        <option value="?...&per_page=20" selected>20</option>
        <option value="?...&per_page=50">50</option>
        <option value="?...&per_page=100">100</option>
    </select>
</div>
```

#### B. Page Information
```html
<div class="pagination-info">
    Showing 21-40 of 156 records | Page 2 of 8
</div>
```

#### C. Navigation Controls
```html
<div class="pagination-nav">
    <a href="?page=1" class="page-link">First</a>
    <a href="?page=1" class="page-link">Previous</a>
    
    <!-- Page numbers -->
    <a href="?page=1" class="page-link">1</a>
    <span class="page-ellipsis">...</span>
    <a href="?page=5" class="page-link">5</a>
    <span class="page-link active">6</span>
    <a href="?page=7" class="page-link">7</a>
    <span class="page-ellipsis">...</span>
    <a href="?page=10" class="page-link">10</a>
    
    <a href="?page=7" class="page-link">Next</a>
    <a href="?page=10" class="page-link">Last</a>
</div>
```

### 5. Page Number Display Logic

**Algorithm for showing page numbers:**
```
If totalPages <= 7:
    Show all pages: [1] [2] [3] [4] [5] [6] [7]

If currentPage <= 4:
    Show: [1] [2] [3] [4] [5] ... [last]

If currentPage >= totalPages - 3:
    Show: [1] ... [last-4] [last-3] [last-2] [last-1] [last]

Otherwise:
    Show: [1] ... [current-1] [current] [current+1] ... [last]
```

### 6. CSS Styling

**Pagination Container:**
```css
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.pagination-nav {
    display: flex;
    gap: 5px;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    background: white;
    color: #007bff;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s;
}

.page-link:hover {
    background: #007bff;
    color: white;
}

.page-link.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.page-link.disabled {
    pointer-events: none;
    opacity: 0.5;
}

.page-ellipsis {
    padding: 8px 12px;
    color: #6c757d;
}
```

**Responsive Design:**
```css
@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .pagination-nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    /* Hide some page numbers on mobile */
    .page-link:not(.active):not(:first-child):not(:last-child) {
        display: none;
    }
}
```

## Implementation Steps

### Step 1: Update dashboard.php - Add Pagination Variables
```php
// After search parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) && in_array($_GET['per_page'], [10, 20, 50, 100]) 
    ? intval($_GET['per_page']) : 20;
$offset = ($page - 1) * $perPage;
```

### Step 2: Modify Database Query
```php
// Count total records first
$countSql = "SELECT COUNT(*) as total FROM patients WHERE ...";
$countStmt = $conn->prepare($countSql);
// bind params and execute
$countResult = $countStmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $perPage);

// Then get paginated records
$sql .= " LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
// bind params including limit and offset
```

### Step 3: Create Helper Functions
```php
function buildPaginationUrl($page, $perPage = null) {
    // Implementation
}

function generatePageNumbers($currentPage, $totalPages) {
    // Implementation
}
```

### Step 4: Add Pagination UI
```html
<?php if ($totalPages > 1): ?>
<div class="pagination-container">
    <!-- Records per page selector -->
    <!-- Page info -->
    <!-- Navigation controls -->
</div>
<?php endif; ?>
```

### Step 5: Update CSS
Add pagination styles to `assets/css/style.css`

### Step 6: Testing
- Test with different record counts
- Test page navigation
- Test per-page selector
- Test with search parameters
- Test edge cases (page 0, page > total, etc.)
- Test responsive design

## Edge Cases to Handle

1. **No Results**: Don't show pagination
2. **Single Page**: Don't show pagination or show disabled controls
3. **Invalid Page Number**: Redirect to page 1 or last valid page
4. **Invalid Per Page**: Use default value (20)
5. **Page > Total Pages**: Redirect to last page
6. **Empty Search**: Show message, no pagination

## Performance Considerations

1. **Index Optimization**: Ensure indexes on search columns
2. **COUNT Query**: Cache count result if possible
3. **LIMIT/OFFSET**: Efficient for small-medium datasets
4. **Consider Cursor-based**: For very large datasets (future enhancement)

## Accessibility

1. Use semantic HTML (`<nav>` for pagination)
2. Add ARIA labels for screen readers
3. Keyboard navigation support
4. Clear focus indicators
5. Descriptive link text

## Example Implementation

```php
// Complete pagination logic
if ($searched && !empty($patients)) {
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = in_array($_GET['per_page'] ?? 20, [10,20,50,100]) 
        ? intval($_GET['per_page']) : 20;
    
    // Count query
    $countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
    // Execute count query
    
    // Paginated query
    $sql .= " LIMIT ? OFFSET ?";
    $offset = ($page - 1) * $perPage;
    // Execute paginated query
    
    // Calculate pagination
    $totalPages = ceil($totalRecords / $perPage);
    $startRecord = $offset + 1;
    $endRecord = min($offset + $perPage, $totalRecords);
}
```

## Future Enhancements

1. **AJAX Pagination**: Load results without page reload
2. **Infinite Scroll**: Auto-load more results on scroll
3. **Jump to Page**: Input field to jump to specific page
4. **Remember Preference**: Save per-page preference in session/cookie
5. **Export Results**: Export current page or all results
6. **Bulk Actions**: Select multiple records across pages

## Testing Checklist

- [ ] Pagination shows correctly with 0 results
- [ ] Pagination shows correctly with 1-10 results
- [ ] Pagination shows correctly with 11-20 results
- [ ] Pagination shows correctly with 100+ results
- [ ] Page navigation works (First, Prev, Next, Last)
- [ ] Page numbers display correctly
- [ ] Per-page selector works
- [ ] Search parameters maintained across pages
- [ ] Invalid page numbers handled gracefully
- [ ] Responsive design works on mobile
- [ ] Keyboard navigation works
- [ ] Screen reader accessible

---

**Ready for Implementation**: Switch to code mode to implement this pagination system.
