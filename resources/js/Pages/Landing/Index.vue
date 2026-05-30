<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const features = [
  {
    icon: '🔗',
    title: 'קישור אישי להזמנת תורים',
    description: 'כל עסק מקבל קישור אישי שאפשר לשלוח ללקוחות בוואטסאפ, באינסטגרם או באתר.',
  },
  {
    icon: '📅',
    title: 'לוח זמינות חכם',
    description: 'הגדירו ימי עבודה, שעות פעילות וזמנים זמינים להזמנה בצורה פשוטה וברורה.',
  },
  {
    icon: '🔐',
    title: 'אימות OTP ב-SMS',
    description: 'כל לקוח מאמת את מספר הטלפון לפני קביעת התור, כדי להפחית הזמנות מזויפות.',
  },
  {
    icon: '✅',
    title: 'אישור או דחיית תורים',
    description: 'בחרו אם תורים יאושרו אוטומטית או ימתינו לאישור מנהל העסק.',
  },
  {
    icon: '🌍',
    title: 'תמיכה ב-3 שפות',
    description: 'עברית, ערבית ואנגלית — כדי שכל לקוח יוכל להזמין בשפה שנוחה לו.',
  },
  {
    icon: '📊',
    title: 'דאשבורד ניהול לעסק',
    description: 'ניהול שירותים, תורים, זמינות, בקשות ממתינות ולוח יומי במקום אחד.',
  },
]

const businessTypes = [
  'קליניקות',
  'מספרות',
  'סלוני יופי',
  'עורכי דין',
  'יועצים',
  'מאמנים',
]

const pricingFeatures = [
  'קישור אישי להזמנת תורים',
  'ניהול שירותים ללא הגבלה',
  'ניהול זמינות ושעות עבודה',
  'אימות לקוחות באמצעות OTP',
  'אישור ודחיית תורים',
  'דאשבורד עסקי מלא',
  'תמיכה בעברית, ערבית ואנגלית',
]
</script>

<template>
  <Head title="IRestTOR - Smart Appointment Booking" />

  <main class="landing-page" :dir="locale === 'he' || locale === 'ar' ? 'rtl' : 'ltr'">
    <!-- Navbar -->
    <header class="site-header">
      <div class="container landing-nav">
        <Link href="/" class="brand">
          <div class="brand-mark">T</div>
          <span>IRestTOR</span>
        </Link>

        <nav class="nav-links">
          <a href="#features">יתרונות</a>
          <a href="#how">איך זה עובד</a>
          <a href="#pricing">מחיר</a>
        </nav>

        <div class="nav-actions">
          <div class="language-switcher">
            <button>AR</button>
            <button>EN</button>
            <button class="active">HE</button>
          </div>

          <Link href="/login" class="login-link">כניסה</Link>

          <a href="#contact" class="primary-nav-btn">
            צרו קשר
            <span>←</span>
          </a>
        </div>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero-section">
      <div class="hero-glow"></div>

      <div class="container text-center hero-content">
        <div class="hero-badge">
          <span></span>
          חדש · בעברית, אנגלית וערבית
        </div>

        <h1>
          מערכת ניהול התורים שלכם,
          <br />
          מוכנה תוך דקות
        </h1>

        <p>
          בלי טלפונים. בלי הודעות מבולגנות. בלי כאב ראש.
          לקוחות קובעים תור לבד, ואתם מנהלים הכול ממקום אחד.
        </p>

        <div class="hero-actions">
          <a href="#contact" class="main-cta">
            ניסיון חינם · 14 יום
            <span>←</span>
          </a>

          <a href="#demo" class="secondary-cta">
            <span>▶</span>
            צפייה בהדגמה
          </a>
        </div>
      </div>
    </section>

    <!-- Product mockup -->
    <section id="demo" class="mockup-section">
      <div class="container">
        <div class="browser-mockup">
          <div class="browser-top">
            <div class="browser-status">
              live
              <span></span>
            </div>

            <div class="browser-url">
              iresttor.com/book/clinic-x
            </div>

            <div class="browser-dots">
              <span></span>
              <span></span>
              <span></span>
            </div>
          </div>

          <div class="mockup-body">
            <div class="mockup-left">
              <div class="booking-card active">
                <div>
                  <strong>בדיקת ייעוץ</strong>
                  <small>30 דקות</small>
                </div>
                <span>₪120</span>
              </div>

              <div class="date-row">
                <div class="date-card active">
                  <small>Mon</small>
                  <strong>24</strong>
                  <span>Available</span>
                </div>

                <div class="date-card">
                  <small>Tue</small>
                  <strong>25</strong>
                  <span>Available</span>
                </div>

                <div class="date-card">
                  <small>Wed</small>
                  <strong>26</strong>
                  <span>Available</span>
                </div>
              </div>

              <div class="time-row">
                <button class="active">09:00</button>
                <button>10:30</button>
                <button>12:00</button>
              </div>

              <button class="mockup-continue">המשך להזמנה ←</button>
            </div>

            <div class="mockup-right">
              <div class="business-avatar">C</div>
              <h3>ClinicX</h3>
              <p>פתוח היום · 09:00–17:00</p>

              <div class="side-stat active">
                <span>תורים היום</span>
                <strong>12</strong>
              </div>

              <div class="side-stat">
                <span>בקשות ממתינות</span>
                <strong>3</strong>
              </div>

              <div class="side-stat">
                <span>שירותים פעילים</span>
                <strong>8</strong>
              </div>
            </div>

            <div class="floating-confirmation">
              <small>אימות הושלם</small>
              <strong>OTP · #1247 ✓</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="section-block">
      <div class="container">
        <div class="section-heading text-center">
          <span>כל מה שעסק צריך</span>
          <h2>
            הכלים הכבדים — מוכנים מראש.
            <br />
            <strong>אתם רק מנהלים.</strong>
          </h2>
          <p>
            מערכת אחת שמרכזת את כל תהליך קביעת התורים, מהקישור הראשון ועד אישור ההגעה.
          </p>
        </div>

        <div class="features-grid">
          <article v-for="feature in features" :key="feature.title" class="feature-card">
            <div class="feature-icon">{{ feature.icon }}</div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.description }}</p>
          </article>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section id="how" class="how-section">
      <div class="container">
        <div class="section-heading text-center">
          <span>איך זה עובד</span>
          <h2>שלושה צעדים. בלי הפתעות.</h2>
        </div>

        <div class="steps-line">
          <div class="step-item">
            <div class="step-number">01</div>
            <h3>נרשמים</h3>
            <p>פותחים עסק במערכת ומגדירים שם, לוגו, צבעים ופרטי התקשרות.</p>
          </div>

          <div class="step-item">
            <div class="step-number">02</div>
            <h3>מוסיפים שירותים</h3>
            <p>מגדירים שירותים, משך טיפול, מחיר, זמינות ואופן אישור התור.</p>
          </div>

          <div class="step-item">
            <div class="step-number">03</div>
            <h3>משתפים קישור</h3>
            <p>שולחים ללקוחות קישור אישי והם קובעים תור בעצמם בזמן שנוח להם.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Suitable businesses -->
    <section class="businesses-section">
      <div class="container text-center">
        <span class="mini-label">מתאים ל...</span>
        <h2>עסק קטן, צוות גדול, קליניקה או נותן שירות.</h2>
        <p>IRestTOR מתאים לעסקים שמקבלים לקוחות לפי תורים.</p>

        <div class="business-pills">
          <div v-for="type in businessTypes" :key="type" class="business-pill">
            {{ type }}
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="pricing-section">
      <div class="container">
        <div class="section-heading text-center">
          <span>מחיר</span>
          <h2>
            מחיר אחד, ברור.
            <strong>הכל כלול.</strong>
          </h2>
          <p>תוכנית פשוטה בלי כאב ראש ובלי הפתעות.</p>
        </div>

        <div class="pricing-card">
          <div class="popular-badge">כל היתרונות כלולים</div>

          <h3>IRestTOR</h3>
          <p>כל מה שצריך כדי לנהל תורים בצורה מקצועית.</p>

          <div class="price">
            ₪249
            <span>/ חודש</span>
          </div>

          <ul>
            <li v-for="item in pricingFeatures" :key="item">
              <span>✓</span>
              {{ item }}
            </li>
          </ul>

          <a href="#contact" class="pricing-btn">
            התחילו עכשיו · 14 יום חינם
            <span>←</span>
          </a>
        </div>
      </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="contact-section">
      <div class="container">
        <div class="contact-heading text-center">
          <span>צרו קשר</span>
          <h2>מוכנים להפסיק לנהל תורים בהודעות?</h2>
          <p>השאירו פרטים ונחזור אליכם בהקדם.</p>
        </div>

        <form class="contact-form">
          <div class="row g-3">
            <div class="col-md-6">
              <label>שם מלא</label>
              <input type="text" placeholder="ישראל ישראלי" />
            </div>

            <div class="col-md-6">
              <label>שם העסק</label>
              <input type="text" placeholder="הקליניקה שלי" />
            </div>

            <div class="col-md-6">
              <label>טלפון</label>
              <input type="text" placeholder="054-123-4567" />
            </div>

            <div class="col-md-6">
              <label>סוג העסק</label>
              <select>
                <option>בחרו...</option>
                <option>קליניקה</option>
                <option>מספרה</option>
                <option>סלון יופי</option>
                <option>ייעוץ</option>
                <option>אחר</option>
              </select>
            </div>

            <div class="col-12">
              <label>ספרו לנו קצת על העסק</label>
              <textarea rows="4" placeholder="כמה תורים אתם מקבלים ביום?"></textarea>
            </div>

            <div class="col-12">
              <button type="button">
                שליחת פרטים
                <span>←</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </section>
  </main>
</template>
