<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vision Care Clinic</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <header id="header" class="animate-slide-down">
    <nav>
      <div class="logo">
        <i class="fas fa-eye"></i>
        Vision Care Clinic
      </div>
      <ul class="nav-links" id="navLinks">
        <li><a href="#home" class="active">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#our-team">Our Doctors</a></li>
        <li><a href="#accessories">Accessories</a></li>
        <li><a href="regsiter.php">Register</a></li>
        <li><a href="#contact">Contact</a></li>
        <li id="authLinks">
          <a href="login admin.php" class="login-btn">
            <i class="fas fa-user"></i> Login
          </a>
        </li>
        <li id="userMenuLinks" style="display: none;">
          <a href="#" onclick="showUserDashboard()">
            <i class="fas fa-dashboard"></i> My Dashboard
          </a>
          <a href="#" onclick="handleLogout()">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </li>
      </ul>
      <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
      </button>
    </nav>
  </header>

  <main>
    <section id="home" class="hero">
      <div class="hero-content animate-fade-in">
        <h1>Expert Eye Care Services</h1>
        <p class="subtitle">Your vision is our priority. Schedule an appointment today.</p>
        <div class="cta-buttons">
          <button class="cta-button primary" onclick="location.href='regsiter.php'">
            <i class="fas fa-calendar-plus"></i> Register Now
          </button>
          <button class="cta-button secondary" onclick="location.href='#services'">
            <i class="fas fa-info-circle"></i> Learn More
          </button>
        </div>
      </div>
      <div class="hero-image animate-slide-up">
        <i class="fas fa-eye-dropper"></i>
      </div>
    </section>

    <section id="services" class="services">
      <h2 class="section-title animate-slide-up">Our Services</h2>
      <div class="services-grid">
        <div class="service-card animate-fade-in">
          <i class="fas fa-glasses"></i>
          <h3>Eye Examinations</h3>
          <p>Comprehensive eye health and vision testing</p>
        </div>
        <div class="service-card animate-fade-in" style="animation-delay: 0.2s;">
          <i class="fas fa-eye"></i>
          <h3>Contact Lens Fitting</h3>
          <p>Expert fitting and consultation for all types of contact lenses</p>
        </div>
        <div class="service-card animate-fade-in" style="animation-delay: 0.4s;">
          <i class="fas fa-brain"></i>
          <h3>Vision Therapy</h3>
          <p>Specialized treatments to improve visual function</p>
        </div>
        <div class="service-card animate-fade-in" style="animation-delay: 0.6s;">
          <i class="fas fa-heartbeat"></i>
          <h3>Eye Disease Management</h3>
          <p>Treatment and monitoring of eye conditions</p>
        </div>
      </div>
    </section>

    </section>
    <section id="our-team" class="our-team">
    <h2>Meet Our Team</h2>
    <div class="team-members">
        <div class="team-member">
            <div class="team-member-front">
                <img src="asset/dr1.jpg" alt="Dr.Anand Palimkar" />
                <h3>Dr.Anand Palimkar</h3>
            </div>
            <div class="team-member-back">
                <h3>Dr.Anand Palimkar</h3>
                <p class="bio">Dr. Anand Palimkar is a distinguished ophthalmologist based in Pune, India, with over 27 years of experience in the field.</p>
                <div class="contact">
                    <p> <b>Email:</b> anand10@gmail.com</p>
                    <p><b>Specialties:</b>Ophthalmologist </p>
                </div>
            </div>
        </div>

        <div class="team-member">
            <div class="team-member-front">
                <img src="asset/dr3.jpg" alt="Dr. Nitin Prabhudesai" />
                <h3>Dr. Nitin Prabhudesai</h3>
            </div>
            <div class="team-member-back">
                <h3>Dr. Nitin Prabhudesai</h3>
                <p class="bio">Dr. Nitin Prabhudesai is a highly experienced ophthalmologist based in Pune, India, with over 33 years of expertise in the field.</p>
                <div class="contact">
                    <p> <b>Email:</b> Nitin87@gmail.com</p>
                    <p> <b>Specialties:</b>Ophthalmologist </p>
                </div>
            </div>
        </div>

        <div class="team-member">
            <div class="team-member-front">
                <img src="asset/dr4.jpg" alt="Dr. Snehal Wakchaure" />
                <h3>Dr. Snehal Wakchaure</h3>
            </div>
            <div class="team-member-back">
                <h3>Dr. Snehal Wakchaure</h3>
                <p class="bio">She specializes in cataract surgery, glaucoma management, general ophthalmology, anterior segment treatments, and medical retina care.</p>
                <div class="contact">
                    <p><b>Email:</b> snehal55@gmail.com</p>
                    <p><b>Specialties:</b> Head - Clinical Service</p>
                </div>
            </div>
        </div>

        <div class="team-member">
            <div class="team-member-front">
                <img src="asset/dr6.jpg" alt="Dr. Ritesh Kakrania" />
                <h3>Dr. Ritesh Kakrania</h3>
            </div>
            <div class="team-member-back">
                <h3>Dr. Ritesh Kakrania</h3>
                <p class="bio">Dr.Ritesh Kakrania is dedicated to providing the highest standard of eye care to patients, ensuring both their visual health and overall well-being.</p>
                <div class="contact">
                    <p><b>Email:</b> ritesh88@gmail.com</p>
                    <p><b>Specialties:</b> Consultant</p>
                </div>
            </div>
        </div>
    </div>
</section>

    <section id="accessories" class="accessories">
      <h2 class="section-title animate-slide-up">Our Accessories</h2>
      <div class="container">
        <div class="card animate-fade-in">
          <img src="asset/as1.webp" alt="Stylish Glasses">
          <h3>Premium Sunglasses</h3>
          <p>Elegant and modern glasses for everyday wear.</p>
          <p class="price">₹1500</p>
          <button onclick="location.href='regsiter.php'">Add to Cart</button>
        </div>
        <div class="card animate-fade-in" style="animation-delay: 0.2s;">
          <img src="asset/as2.webp" alt="Lens Case">
          <h3>Contact Lens Case</h3>
          <p>Protective case for your contact lenses.</p>
          <p class="price">₹319</p>
          <button onclick="location.href='regsiter.php'">Add to Cart</button>
        </div>
        <div class="card animate-fade-in" style="animation-delay: 0.4s;">
          <img src="asset/as3.webp" alt="Cleaning Kit">
          <h3>Cleaning Kit</h3>
          <p>Complete set for keeping your glasses clean.</p>
          <p class="price">₹349</p>
          <button onclick="location.href='regsiter.php'">Add to Cart</button>
        </div>
        <div class="card animate-fade-in" style="animation-delay: 0.6s;">
          <img src="asset/as4.jpg" alt="Sunglasses">
          <h3>Designer Sunglasses</h3>
          <p>UV-protected stylish sunglasses.</p>
          <p class="price">₹4000</p>
          <button onclick="location.href='regsiter.php'">Add to Cart</button>
        </div>
        <div class="card animate-fade-in" style="animation-delay: 0.8s;">
          <img src="asset/as5.jpg" alt="Sunglasses">
          <h3>Screen Glasses</h3>
          <p>Digital devices like phones, laptops, and tablets emit harmful blue rays that can damage our eyes</p>
          <p class="price">₹600</p>
          <button onclick="location.href='regsiter.php'">Add to Cart</button>
        </div>
        <div class="card animate-fade-in" style="animation-delay: 0.8s;">
          <img src="asset/as6.webp" alt="Sunglasses">
          <h3>Sport Sunglasses</h3>
          <p>UV-protected stylish sunglasses for active lifestyles.</p>
          <p class="price">₹1500</p>
          <button onclick="location.href='regsiter.php'">Add to Cart</button>
        </div>
      </div>
    </section>
    

    <section id="contact" class="contact">
      <h2 class="section-title animate-slide-up">Contact Us</h2>
      <div class="contact-container">
        <div class="contact-info animate-fade-in">
          <div class="contact-card">
            <i class="fas fa-map-marker-alt"></i>
            <h3>Address</h3>
            <p>B4, Second Floor, Office, Shree Sadashiv Housing Society<br> 01 Fergusson College Rd, Shivajinagar<br> Pune Maharashtra 411016</p>
          </div>
          <div class="contact-card">
            <i class="fas fa-phone"></i>
            <h3>Phone</h3>
            <p>8806526090</p>
          </div>
          <div class="contact-card">
            <i class="fas fa-envelope"></i>
            <h3>Email</h3>
            <p>info@visioncareclinic.com</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="animate-slide-up">
    <div class="footer-content">
      <div class="footer-section">
        <h4><i class="fas fa-eye"></i> Vision Care Clinic</h4>
        <p>Providing quality eye care since 2024</p>
      </div>
      <div class="footer-section">
        <h4>Hours</h4>
        <p>Monday - Friday: 9am - 6pm<br>Saturday: 9am - 2pm<br>Sunday: Closed</p>
      </div>
      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#services">Services</a></li>
          <li><a href="regsiter.php">Register</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 Vision Care Clinic. All rights reserved.</p>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    
    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        const icon = mobileMenuBtn.querySelector('i');
        if (icon) {
          icon.classList.toggle('fa-bars');
          icon.classList.toggle('fa-times');
        }
      });
    }
    
    // Navbar scroll effect
    window.addEventListener('scroll', () => {
      const header = document.getElementById('header');
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Close mobile menu if open
        if (navLinks.classList.contains('active')) {
          navLinks.classList.remove('active');
          mobileMenuBtn.querySelector('i').classList.remove('fa-times');
          mobileMenuBtn.querySelector('i').classList.add('fa-bars');
        }
        
        // Smooth scroll to target
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
          
          // Update active link
          document.querySelectorAll('.nav-links a').forEach(link => {
            link.classList.remove('active');
          });
          this.classList.add('active');
        }
      });
    });
    
    // Update active nav link on scroll
    window.addEventListener('scroll', () => {
      const sections = document.querySelectorAll('section');
      let currentSection = '';
      
      sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (window.scrollY >= (sectionTop - 200)) {
          currentSection = section.getAttribute('id');
        }
      });
      
      document.querySelectorAll('.nav-links a').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === #${currentSection}) {
          link.classList.add('active');
        }
      });
    });
    
    // Authentication function stubs (to maintain existing logic)
    function showUserDashboard() {
      // Maintain existing logic
      console.log('Navigate to user dashboard');
    }
    
    function handleLogout() {
      // Maintain existing logic
      console.log('User logged out');
    }
    
    // Intersection Observer for animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -
    }
    </body>
    </html>