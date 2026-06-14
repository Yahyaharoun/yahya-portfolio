import os

filepath = 'app/Http/Middleware/HandleInertiaRequests.php'
with open(filepath, 'r') as f:
    content = f.read()

content = content.replace(
    "$newPartnerships = Partnership::where('status', 'new')->count();",
    "$lastPartnershipsView = cache()->get('last_contracts_view', now()->subDays(30));\n            $newPartnerships = Partnership::where('created_at', '>', $lastPartnershipsView)->count();"
).replace(
    "$newCvDownloads  = CvDownload::where('created_at', '>=', now()->subDays(7))->count();",
    "$lastCvDownloadsView = cache()->get('last_cv_downloads_view', now()->subDays(30));\n            $newCvDownloads  = CvDownload::where('created_at', '>', $lastCvDownloadsView)->count();"
)

with open(filepath, 'w') as f:
    f.write(content)
print("Middleware patched.")

dashboard = 'app/Http/Controllers/Admin/DashboardController.php'
with open(dashboard, 'r') as f:
    d_content = f.read()

d_content = d_content.replace(
    "$newPartnerships = Partnership::where('status', 'new')->count();",
    "$lastPartnershipsView = cache()->get('last_contracts_view', now()->subDays(30));\n        $newPartnerships = Partnership::where('created_at', '>', $lastPartnershipsView)->count();"
).replace(
    "$newCvDownloads = CvDownload::where('created_at', '>=', now()->subDays(7))->count();",
    "$lastCvDownloadsView = cache()->get('last_cv_downloads_view', now()->subDays(30));\n        $newCvDownloads = CvDownload::where('created_at', '>', $lastCvDownloadsView)->count();"
)

with open(dashboard, 'w') as f:
    f.write(d_content)
print("Dashboard patched.")
