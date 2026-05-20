<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Tourism Service - World-Class Healthcare Abroad</title>
    <meta name="description" content="Access world-class medical treatments, hotel stays, transportation, and visa assistance — all in one place.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#0ea5e9', dark: '#0284c7', light: '#e0f2fe' },
                        accent:  { DEFAULT: '#f59e0b', dark: '#d97706' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    animation: {
                        'fade-up': 'fadeUp 0.6s ease forwards',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%':   { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: linear-gradient(135deg, #0c4a6e 0%, #0ea5e9 50%, #38bdf8 100%);
        }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(14,165,233,0.2); }
        .nav-link { transition: color 0.2s ease; }
        .nav-link:hover { color: #38bdf8; }
        .slide-in { animation: fadeUp 0.6s ease forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

<!-- Navigation -->
<header class="bg-slate-900/95 backdrop-blur sticky top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="text-white font-bold text-lg tracking-tight">MedTour <span class="text-sky-400">Services</span></span>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            <a href="index.php"       class="nav-link text-slate-300 font-medium text-sm">Home</a>
            <a href="services.php"    class="nav-link text-slate-300 font-medium text-sm">Services</a>
            <a href="help.php"        class="nav-link text-slate-300 font-medium text-sm">Help</a>
            <a href="login_admin.php" class="nav-link text-slate-300 font-medium text-sm">Admin</a>
            <a href="login_doctor.php" class="nav-link text-slate-300 font-medium text-sm">Doctor</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="login_user.php"  class="text-sky-400 border border-sky-400 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-sky-400 hover:text-white transition">Login</a>
            <a href="signup_user.php" class="bg-sky-500 hover:bg-sky-600 text-white rounded-lg px-4 py-2 text-sm font-semibold transition">Sign Up</a>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="hero-bg text-white py-28 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('background.png')] bg-cover bg-center"></div>
    <div class="max-w-4xl mx-auto text-center relative z-10 slide-in">
        <span class="inline-block bg-sky-400/20 border border-sky-400/40 text-sky-200 text-xs font-semibold uppercase tracking-widest px-4 py-1.5 rounded-full mb-6">World-Class Healthcare Abroad</span>
        <h1 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6">
            Your Journey to<br><span class="text-amber-400">Better Health</span> Starts Here
        </h1>
        <p class="text-sky-100 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
            Access premium medical treatments worldwide with full travel support — hotels, transport, visa, and hospital bookings in one seamless platform.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="login_user.php" class="bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold px-8 py-4 rounded-xl text-lg transition shadow-lg hover:shadow-amber-400/40">
                Get Started →
            </a>
            <a href="help.php" class="border border-white/40 hover:bg-white/10 text-white font-semibold px-8 py-4 rounded-xl text-lg transition">
                Learn More
            </a>
        </div>
    </div>
    <!-- Decorative blobs -->
    <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-sky-300/20 rounded-full blur-3xl"></div>
    <div class="absolute -top-16 -right-16 w-80 h-80 bg-blue-600/20 rounded-full blur-3xl"></div>
</section>

<!-- Stats Bar -->
<section class="bg-white border-b border-slate-100 py-8">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div>
            <p class="text-3xl font-bold text-sky-600">50+</p>
            <p class="text-slate-500 text-sm mt-1">Partner Hospitals</p>
        </div>
        <div>
            <p class="text-3xl font-bold text-sky-600">10K+</p>
            <p class="text-slate-500 text-sm mt-1">Patients Served</p>
        </div>
        <div>
            <p class="text-3xl font-bold text-sky-600">30+</p>
            <p class="text-slate-500 text-sm mt-1">Countries</p>
        </div>
        <div>
            <p class="text-3xl font-bold text-sky-600">98%</p>
            <p class="text-slate-500 text-sm mt-1">Satisfaction Rate</p>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-20 px-4 bg-slate-50">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-4xl font-bold text-slate-800 mb-4">Our Services</h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto">Everything you need for a smooth medical journey — from booking to arrival.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Hotel -->
            <a href="hotel.php" class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col items-center text-center group">
                <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-sky-500 transition">
                    <svg class="w-8 h-8 text-sky-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-lg mb-2">Hotel Booking</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Comfortable stays near top medical facilities worldwide.</p>
            </a>
            <!-- Transport -->
            <a href="transport.php" class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col items-center text-center group">
                <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-500 transition">
                    <svg class="w-8 h-8 text-emerald-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l-5-5m0 0l5-5m-5 5h18"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-lg mb-2">Transportation</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Reliable airport pickups, hospital transfers, and city rides.</p>
            </a>
            <!-- Hospital -->
            <a href="hospital.php" class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col items-center text-center group">
                <div class="w-16 h-16 bg-rose-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-rose-500 transition">
                    <svg class="w-8 h-8 text-rose-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-lg mb-2">Hospital Booking</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Book appointments at leading hospitals with top specialists.</p>
            </a>
            <!-- Visa -->
            <a href="visa.php" class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col items-center text-center group">
                <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-violet-500 transition">
                    <svg class="w-8 h-8 text-violet-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-lg mb-2">Visa Assistance</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Hassle-free medical visa applications and processing support.</p>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-sky-500 font-semibold text-sm uppercase tracking-widest">About Medical Tourism</span>
            <h2 class="text-4xl font-bold text-slate-800 mt-3 mb-6 leading-tight">Why Choose Medical Travel?</h2>
            <div class="space-y-4 text-slate-600 leading-relaxed">
                <p>Medical tourism allows patients to access specialized treatments, advanced technologies, and world-class specialists at a fraction of the local cost — without compromising on quality.</p>
                <p>Popular destinations like Thailand, Turkey, India, and Malaysia offer internationally accredited hospitals with skilled professionals and affordable treatment options.</p>
                <p>Our platform streamlines every step — from choosing a specialist to booking accommodation and arranging airport transfers — so you can focus on your recovery.</p>
            </div>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="signup_user.php" class="bg-sky-600 hover:bg-sky-700 text-white font-semibold px-6 py-3 rounded-xl transition">Create Account</a>
                <a href="help.php" class="text-sky-600 border border-sky-300 hover:border-sky-500 font-semibold px-6 py-3 rounded-xl transition">Learn More</a>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-sky-50 rounded-2xl p-6 border border-sky-100">
                <div class="text-3xl font-bold text-sky-600 mb-1">60%</div>
                <div class="text-slate-600 text-sm">Cost savings vs domestic treatment</div>
            </div>
            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                <div class="text-3xl font-bold text-amber-600 mb-1">2x</div>
                <div class="text-slate-600 text-sm">Faster appointment scheduling</div>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100">
                <div class="text-3xl font-bold text-emerald-600 mb-1">24/7</div>
                <div class="text-slate-600 text-sm">Dedicated patient support</div>
            </div>
            <div class="bg-violet-50 rounded-2xl p-6 border border-violet-100">
                <div class="text-3xl font-bold text-violet-600 mb-1">JCI</div>
                <div class="text-slate-600 text-sm">Accredited partner hospitals</div>
            </div>
        </div>
    </div>
</section>

<!-- Image Gallery -->
<section class="py-16 px-4 bg-slate-50">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-slate-800 mb-10">A Glimpse of Our Services</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="rounded-2xl overflow-hidden shadow-md">
                <img src="slide1.jpg" alt="Medical tourism" class="w-full h-56 object-cover hover:scale-105 transition duration-500">
            </div>
            <div class="rounded-2xl overflow-hidden shadow-md">
                <img src="slide2.jpg" alt="Healthcare facility" class="w-full h-56 object-cover hover:scale-105 transition duration-500">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gradient-to-r from-sky-600 to-blue-700 py-16 px-4 text-white text-center">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-4xl font-bold mb-4">Ready to Begin Your Journey?</h2>
        <p class="text-sky-100 text-lg mb-8">Join thousands of patients who've trusted MedTour Services for their medical travel needs.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="signup_user.php" class="bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold px-8 py-4 rounded-xl text-lg transition shadow-lg">
                Create Your Account
            </a>
            <a href="login_user.php" class="border border-white/50 hover:bg-white/10 text-white font-semibold px-8 py-4 rounded-xl text-lg transition">
                Sign In
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-slate-900 text-slate-400 py-8 px-4 text-center text-sm">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
    <div class="flex justify-center gap-6 mt-3">
        <a href="index.php" class="hover:text-white transition">Home</a>
        <a href="services.php" class="hover:text-white transition">Services</a>
        <a href="help.php" class="hover:text-white transition">Help</a>
        <a href="login_admin.php" class="hover:text-white transition">Admin</a>
    </div>
</footer>

</body>
</html>
