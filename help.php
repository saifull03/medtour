<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Information – Medical Tourism Service</title>
    <meta name="description" content="Learn everything about medical tourism and how MedTour Services can help you plan your medical journey abroad.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-50 min-h-screen">

<!-- Header -->
<header class="bg-slate-900/95 backdrop-blur sticky top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="text-white font-bold text-lg tracking-tight">MedTour <span class="text-sky-400">Services</span></span>
        </a>
        <nav class="hidden md:flex items-center gap-6">
            <a href="index.php"       class="text-slate-300 hover:text-white text-sm font-medium transition">Home</a>
            <a href="services.php"    class="text-slate-300 hover:text-white text-sm font-medium transition">Services</a>
            <a href="help.php"        class="text-sky-400 text-sm font-medium">Help</a>
            <a href="login_admin.php" class="text-slate-300 hover:text-white text-sm font-medium transition">Admin</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="login_user.php"  class="text-sky-400 border border-sky-400 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-sky-400 hover:text-white transition">Login</a>
            <a href="signup_user.php" class="bg-sky-500 hover:bg-sky-600 text-white rounded-lg px-4 py-2 text-sm font-semibold transition">Sign Up</a>
        </div>
    </div>
</header>

<!-- Hero -->
<section class="bg-gradient-to-br from-slate-800 to-sky-900 text-white py-16 px-4 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block bg-sky-400/20 border border-sky-400/40 text-sky-200 text-xs font-semibold uppercase tracking-widest px-4 py-1.5 rounded-full mb-5">Knowledge Center</span>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Help & Information</h1>
        <p class="text-sky-100 text-lg">Everything you need to know about medical tourism and how our platform works.</p>
    </div>
</section>

<main class="max-w-5xl mx-auto px-4 py-16">

    <!-- What is Medical Tourism -->
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 mb-8">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🌍</div>
            <h2 class="text-2xl font-bold text-slate-800">What is Medical Tourism?</h2>
        </div>
        <div class="space-y-4 text-slate-600 leading-relaxed">
            <p>Medical tourism, also known as health tourism or medical travel, refers to the practice of traveling to another country to receive medical treatment or procedures. It has gained significant popularity in recent years due to several key factors.</p>
            <p>Patients seek medical tourism for various reasons: specialized treatments not available in their home country, significantly lower costs compared to domestic healthcare, access to advanced medical technologies, shorter waiting times, and the opportunity to combine treatment with leisure travel.</p>
        </div>
    </div>

    <!-- Why Choose Us -->
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">✅</div>
            <h2 class="text-2xl font-bold text-slate-800">Why Choose MedTour Services?</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-5">
            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl">
                <span class="text-emerald-500 text-lg mt-0.5">💰</span>
                <div>
                    <h3 class="font-semibold text-slate-800 mb-1">Cost Savings</h3>
                    <p class="text-slate-500 text-sm">Save up to 60% on medical procedures compared to domestic pricing without sacrificing quality.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl">
                <span class="text-sky-500 text-lg mt-0.5">🏆</span>
                <div>
                    <h3 class="font-semibold text-slate-800 mb-1">World-Class Quality</h3>
                    <p class="text-slate-500 text-sm">All partner hospitals are JCI-accredited with internationally trained specialist doctors.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl">
                <span class="text-violet-500 text-lg mt-0.5">⚡</span>
                <div>
                    <h3 class="font-semibold text-slate-800 mb-1">Faster Access</h3>
                    <p class="text-slate-500 text-sm">Skip long domestic waiting lists and get appointments within days of booking.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl">
                <span class="text-amber-500 text-lg mt-0.5">🤝</span>
                <div>
                    <h3 class="font-semibold text-slate-800 mb-1">End-to-End Support</h3>
                    <p class="text-slate-500 text-sm">From visa applications to hotel booking and transport — we coordinate everything for you.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">❓</div>
            <h2 class="text-2xl font-bold text-slate-800">Frequently Asked Questions</h2>
        </div>
        <div class="space-y-4">
            <details class="group border border-slate-200 rounded-xl overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer font-semibold text-slate-700 hover:bg-slate-50 transition">
                    How do I create an account?
                    <span class="text-sky-500 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-4 pb-4 text-slate-500 text-sm leading-relaxed">
                    Click the "Sign Up" button in the top navigation bar. Fill in your name, email, and password. Once registered, you can immediately log in and start booking services.
                </div>
            </details>
            <details class="group border border-slate-200 rounded-xl overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer font-semibold text-slate-700 hover:bg-slate-50 transition">
                    What services can I book through MedTour?
                    <span class="text-sky-500 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-4 pb-4 text-slate-500 text-sm leading-relaxed">
                    You can book four core services: Hotel accommodations near hospitals, Transportation (airport pickups, hospital transfers), Hospital appointments with specialists, and Visa assistance for medical visas.
                </div>
            </details>
            <details class="group border border-slate-200 rounded-xl overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Is my personal information secure?
                    <span class="text-sky-500 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-4 pb-4 text-slate-500 text-sm leading-relaxed">
                    Yes. All passwords are securely hashed and your personal data is stored in an encrypted database. We never share your information with third parties without consent.
                </div>
            </details>
            <details class="group border border-slate-200 rounded-xl overflow-hidden">
                <summary class="flex items-center justify-between p-4 cursor-pointer font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Popular destinations for medical tourism?
                    <span class="text-sky-500 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-4 pb-4 text-slate-500 text-sm leading-relaxed">
                    Top destinations include Thailand, Turkey, India, Malaysia, and South Korea — all renowned for world-class healthcare facilities, skilled professionals, and cost-effective treatment options.
                </div>
            </details>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-8 text-white">
        <h2 class="text-2xl font-bold mb-5">Get Started Today</h2>
        <div class="flex flex-wrap gap-4">
            <a href="signup_user.php"  class="bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm">📝 Create Account</a>
            <a href="login_user.php"   class="bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm">🔑 Patient Login</a>
            <a href="services.php"     class="bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm">🏥 View Services</a>
            <a href="login_admin.php"  class="bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm">🛡️ Admin Portal</a>
            <a href="login_doctor.php" class="bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm">👨‍⚕️ Doctor Portal</a>
        </div>
    </div>

</main>

<!-- Footer -->
<footer class="bg-slate-900 text-slate-400 py-8 px-4 text-center text-sm">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
    <div class="flex justify-center gap-6 mt-3">
        <a href="index.php"       class="hover:text-white transition">Home</a>
        <a href="services.php"    class="hover:text-white transition">Services</a>
        <a href="help.php"        class="hover:text-white transition">Help</a>
        <a href="login_admin.php" class="hover:text-white transition">Admin</a>
    </div>
</footer>

</body>
</html>
