import re

with open('app/Http/Controllers/CvController.php', 'r') as f:
    content = f.read()

# 1. Remove $request->ip() from the three methods
content = content.replace("md5($validated['email'] . $request->ip())", "md5($validated['email'])")
content = content.replace("md5($request->email . $request->ip())", "md5($request->email)")

# 2. Add local backdoor for 123456
old_if = "if (!$data || $data['otp'] !== $request->code) {"
new_if = "if (!$data || ($data['otp'] !== $request->code && !(app()->environment('local') && $request->code === '123456'))) {"
content = content.replace(old_if, new_if)

# 3. In processDownload, we also need to allow 123456 if data is somehow missing? No, data must exist because they need to have initiated it. 
# Wait, if they use 123456 but data expired, $data is null, so it fails in processDownload:
# if ($data && isset($data['user_data'])) {
# We should probably extend the time or just ensure they request code first.
# Wait, if $data is null, verifyCode also fails because `!$data` is true!
# So we need to change: if (!$data && !(app()->environment('local') && $request->code === '123456'))
# But if we bypass !$data, processDownload won't have $data['user_data'] to save to DB!
# So $data MUST exist. Thus they must click "Obtenir le code".
# They did that, they just didn't type the right code.

with open('app/Http/Controllers/CvController.php', 'w') as f:
    f.write(content)

print("Updated CvController.php")
