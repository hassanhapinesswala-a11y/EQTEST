<?php
// index.php
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>EQ Test — Measure Emotional Intelligence</title>
<style>
  /* internal CSS - stylish, modern */
  :root{
    --bg:#0f1724; --card:#0b1220; --accent:#6ee7b7; --muted:#9aa7b2; --glass: rgba(255,255,255,0.03);
  }
  *{box-sizing:border-box;font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;}
  body{margin:0;background: linear-gradient(180deg,#071129 0%, #081827 60%); color:#e6f7f0; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:32px;}
  .container{width:100%; max-width:980px; border-radius:16px; padding:28px; background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02)); box-shadow: 0 10px 30px rgba(2,6,23,0.7); border:1px solid rgba(255,255,255,0.03);}
  header{display:flex; gap:20px; align-items:center;}
  .logo{width:64px;height:64px;border-radius:12px;background:linear-gradient(135deg,#083344,#0ad5a8); display:flex;align-items:center;justify-content:center;font-weight:700;color:#042a20; font-size:18px;}
  h1{margin:0;font-size:28px;}
  p.lead{color:var(--muted); margin-top:12px; line-height:1.5;}
  .card-grid{display:grid; grid-template-columns: 1fr 320px; gap:18px; margin-top:20px;}
  .about{padding:18px; background:var(--glass); border-radius:12px; border:1px solid rgba(255,255,255,0.02);}
  .cta{padding:20px;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border-radius:12px; display:flex; flex-direction:column; gap:12px; align-items:center; justify-content:center;}
  .primary-btn{background:linear-gradient(90deg,var(--accent),#34d399); border:none; padding:12px 20px; border-radius:12px; color:#02322a; font-weight:700; font-size:16px; cursor:pointer; box-shadow: 0 6px 18px rgba(46,229,171,0.12);}
  .primary-btn:hover{transform:translateY(-3px); transition:0.18s;}
  .muted{color:var(--muted); font-size:14px;}
  footer{margin-top:18px; color:var(--muted); font-size:13px; text-align:center;}
  @media (max-width:900px){
    .card-grid{grid-template-columns:1fr; }
  }
</style>
</head>
<body>
  <div class="container">
    <header>
      <div class="logo">EQ</div>
      <div>
        <h1>Emotional Intelligence Test</h1>
        <div class="muted">Understand your emotional strengths and growth areas</div>
      </div>
    </header>
 
    <div class="card-grid">
      <div class="about">
        <h2>Why EQ matters</h2>
        <p class="lead">Emotional intelligence (EQ) helps you understand your own emotions and the emotions of others.
        This short test assesses self-awareness, empathy, and emotional regulation — and gives personalized feedback so you can grow.</p>
        <ul class="muted">
          <li>~ 8–20 questions (you can add more in DB)</li>
          <li>Instant score with tailored feedback</li>
          <li>Retake anytime</li>
        </ul>
      </div>
 
      <div class="cta">
        <div style="text-align:center;">
          <strong style="font-size:20px; display:block; margin-bottom:6px;">Ready to start?</strong>
          <div class="muted">Click start and you'll be taken to the quiz page.</div>
        </div>
        <button class="primary-btn" id="startBtn">Start Test</button>
        <div class="muted">Your answers are used only for giving feedback — nothing is stored permanently.</div>
      </div>
    </div>
 
    <footer>
      <small>Created with ❤️ — EQ Test Clone</small>
    </footer>
  </div>
 
<script>
  // JS only for redirection (per your request)
  document.getElementById('startBtn').addEventListener('click', function(){
    // go to quiz page (no PHP redirect)
    window.location.href = 'quiz.php';
  });
</script>
</body>
</html>
 
