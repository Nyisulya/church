# 📋 Nyaraka za Mfumo (Church Management System Documentation)
### Manzese Seventh-Day Adventist Church

Nyaraka hizi zinaelezea vipengele muhimu vya mfumo vilivyoboreshwa na kusanidiwa kwa ajili ya matumizi rasmi ya Kanisa la Manzese SDA (Going Live).

---

## 🔑 1. Usimamizi wa Nenosiri na Ulinzi (Password & Security)

Mfumo umeboreshwa kwa kutoa uhuru kamili kwa watumiaji kujihudumia wenyewe na kuimarisha ulinzi wa akaunti zao.

### A. Kurejesha Nenosiri Lililosahaulika (Forgot Password Flow)
Mwanachama yeyote akisahau password yake ya kuingia kwenye mfumo:
1. Kwenye ukurasa wa **Login**, anabonyeza kiungo cha **Forgot Password?**.
2. Anaweka **barua pepe (email)** yake iliyosajiliwa kwenye mfumo na kubonyeza **Send Reset Link**.
3. Mfumo unamtumia ujumbe wenye kiungo (link) salama kwenye email yake ya Gmail/Yahoo.
4. Mwanachama anafungua email, anabonyeza **Weka Upya Nenosiri** na kuweka password mpya.
* **Ulinzi wa SMTP:** Ikiwa SMTP server imekata au haijasaniwa kwa wakati huo, mfumo hautaleta error (500 crash). Badala yake, utamletea mtumiaji ujumbe mzuri wa kumuelekeza kuwasiliana na Katibu/Admin ili abadilishiwe kwa mkono.

### B. Kubadilisha Nenosiri Ndani ya Mfumo (Change Password Page)
Kila mtumiaji sasa ana uwezo wa kubadilisha password yake kupitia akaunti yake:
1. Kiungo cha **Change Password** kipo kwenye menyu ya kushoto (Sidebar) chini ya *My Profile*.
2. **Uthibitishaji wa Nenosiri la Sasa (Current Password Verification):** Mtumiaji lazima aandike password yake ya sasa (la zamani) ndipo mfumo umruhusu kuweka password mpya. Hii inazuia watu wasio waaminifu kubadilisha nenosiri kama akaunti ikiachwa wazi.
3. Kitufe cha **Jicho (Eye Icon)** kipo kwenye kila kisanduku ili mtumiaji aweze kuona na kuhakiki anachokiandika.

---

## 📇 2. Mfumo wa Mahudhurio wa QR (QR Attendance Scanning)

Mfumo huu unaruhusu utambuzi na uwekaji wa mahudhurio ya washiriki kwa kutumia kamera za simu za watumishi (ushers/deacons) wakati wa ibada au matukio ya kanisa.

### A. QR Kwenye Kadi za ID
* Kadi za ID za wanachama zinabeba kiungo kamili (Link) cha kipekee chenye namba ya mwanachama (k.m. `https://sdachurch.nyisu.com/attendance/scan-qr/MS-0023`).
* Kamera ya simu yoyote ya kawaida ikiskani QR hiyo, itampeleka mhudumu moja kwa moja kwenye ukurasa wa kuweka mahudhurio.

### B. Mchakato wa Usalama (Inline Secure Login)
* **Kama mhudumu hajaingia kwenye mfumo (Logged Out):** Akiskani QR, ukurasa utafungua fomu ya **Inline Secure Login** hapo hapo. Mhudumu ataingia bila kurudishwa kwenye Dashboard, kisha mahudhurio ya mwanachama yatarekodiwa mara moja!
* **Ukurasa wa Matokeo (Scan Result):** Ukurasa huu unajitegemea (Standalone) ili kuzuia error za `unreadNotifications on null` zinazotokea kwa watumiaji ambao hawajaingia kwenye mfumo.

### C. Uchaguzi wa Matukio (Event Selector)
* Kama kuna matukio au ibada zaidi ya moja kwa siku hiyo, mfumo utamwambia mhudumu achague tukio husika (k.m. *Ibada ya Kwanza* au *Shule ya Sabato*) **kwa skana ya kwanza tu**.
* Mfumo utakariri tukio hilo (Session-based cache) kwa skana zote zinazofuata ili mhudumu asilazimike kuchagua kila wakati.

---

## 💬 3. Mawasiliano na SMS za Kiotomatiki (SMS Gateway Integration)

Mfumo umeunganishwa na App ya Android ya **"SMS GATEWAY API" (by SimpApp)** ambayo inageuza simu ya mkononi ya kanisa kuwa Server ya kutuma ujumbe kwa kutumia laini ya simu na kifurushi cha kanisa (bila gharama za ziada za per-SMS).

### A. Sanidi ya .env na Config Cache
Ili kuzuia kukatika kwa mawasiliano pindi cache inapowashwa kwenye server (aaPanel):
* API Key inahifadhiwa kwenye `.env` kama `SMS_GATEWAY_API_KEY`.
* Inasomwa kitaalamu kupitia faili la [config/services.php](file:///c:/xampp/htdocs/projects/manzese/church/config/services.php) (`config('services.sms_gateway.api_key')`).

### B. SMS za Kiotomatiki (Auto-Triggers)
* **Sajili ya Wageni (Visitors welcome):** Kila mgeni mpya akisajiliwa na namba yake ya simu kuwekwa, atapokea SMS ya kiotomatiki ya kumkaribisha Manzese SDA Church.
* **Uthibitishaji wa Michango (Receipts/Confirmation):** Kila mchango wa mwanachama (Zaka, Sadaka, Ujenzi, Shukrani, Miradi) unaporekodiwa, mwanachama atapokea SMS ya kiotomatiki yenye jina lake, kiasi alichotoa, aina ya mchango, na tarehe, ikiambatana na ujumbe wa shukrani.

### C. Mialiko ya Ibada (Bulk SMS)
* Viongozi wanaweza kutumia menyu ya **Communications** kutuma ujumbe wa mialiko ya ibada, mikutano, au dharura kwa kundi zima la waumini kwa mara moja.

---

## 💰 4. Usalama wa Fedha na Ahadi (Financial & Pledge Security)

Ili kulinda uadilifu wa data za fedha na kuzuia washiriki kuweka taarifa za uongo za malipo ya ahadi zao:

1. **Malipo ya Mkono (Offline/Manual Payments):**
   * Fomu ya kujaza malipo kwa mkono (kama vile Cash, manual M-Pesa, Bank Transfer) **imefichwa kabisa kwa wanachama wa kawaida**.
   * Fomu hii inaonekana na inatumiwa **TU** na watumiaji wenye majukumu ya kiutawala au kifedha (`super_admin`, `admin`, `pastor`, `financial_officer`).
   * API ya kuhifadhi malipo haya imelindwa kwa kiwango cha juu cha usalama (`403 Forbidden` ikiwa mtumiaji wa kawaida atajaribu kuifikia).
2. **Malipo ya Mtandaoni (Online Payments):**
   * Waumini wa kawaida wataona kitufe kikubwa cha **Lipa Ahadi Online** ambacho kitawapeleka kwenye lango salama la malipo la Pesapal/Flutterwave. Miamala hii inathibitishwa na kukamilishwa na benki/mitandao ya simu yenyewe kiotomatiki.
   * Maelekezo ya wazi yamewekwa kwa waumini ambao wamelipa kwa Cash au Direct Bank kuwasiliana na Katibu wa Fedha wa Kanisa ili arekodi malipo yako kwenye mfumo.

---

## 🛠️ 5. Amri Muhimu za Kila Mara (Useful Commands for aaPanel Terminal)

Kila mara unapofanya mabadiliko ya faili la `.env` au kuleta mabadiliko kutoka GitHub kwenye VPS yako ya aaPanel, hakikisha unaendesha amri hizi kwenye Terminal:

* **Kupokea Mabadiliko Mapya (Pull updates):**
  ```bash
  git pull
  ```
* **Kusafisha Config Cache:**
  ```bash
  php artisan config:clear
  ```
* **Kusafisha Cache zote za Mfumo:**
  ```bash
  php artisan optimize:clear
  ```
