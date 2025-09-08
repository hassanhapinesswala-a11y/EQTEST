<?php
// quiz.php
require_once 'db.php';
 
// fetch all questions
$stmt = $pdo->query("SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions ORDER BY id ASC");
$questions = $stmt->fetchAll();
 
if (!$questions) {
    echo "<!doctype html><html><body><p>No questions found. Please import schema.sql sample questions.</p></body></html>";
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>EQ Test — Quiz</title>
<style>
  :root{--bg:#071827; --card:#072633; --accent:#7ee7c9; --muted:#9fb7b0;}
  body{margin:0; font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto; background:linear-gradient(180deg,#041021 0%,#071827 100%); color:#ecfff7; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:28px;}
  .wrap{width:100%; max-width:980px; background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); padding:22px; border-radius:14px; border:1px solid rgba(255,255,255,0.03);}
  header{display:flex; justify-content:space-between; align-items:center;}
  header h2{margin:0;}
  .muted{color:var(--muted);}
  form{margin-top:16px;}
  .question-card{background:rgba(255,255,255,0.02); padding:18px; border-radius:12px; margin-bottom:12px; border:1px solid rgba(255,255,255,0.01);}
  .options{display:grid; gap:10px; margin-top:12px;}
  .option{display:flex; align-items:center; gap:12px; background:rgba(0,0,0,0.18); padding:12px; border-radius:10px; cursor:pointer; border:1px solid rgba(255,255,255,0.01);}
  input[type="radio"]{transform:scale(1.15);}
  .controls{display:flex; gap:10px; justify-content:space-between; align-items:center; margin-top:14px;}
  .btn{background:linear-gradient(90deg,var(--accent),#3dd6a9); border:none; padding:10px 14px; border-radius:10px; font-weight:700; color:#02342b; cursor:pointer;}
  .btn-secondary{background:transparent; border:1px solid rgba(255,255,255,0.06); color:var(--muted); padding:10px 14px; border-radius:10px; cursor:pointer;}
  .progress{height:10px; background:rgba(255,255,255,0.03); border-radius:10px; overflow:hidden; margin-top:12px;}
  .progress > i{display:block; height:100%; background:linear-gradient(90deg,#6ee7b7,#34d399); width:0%;}
  @media (max-width:800px){ .options{gap:8px;} }
</style>
</head>
<body>
  <div class="wrap">
    <header>
      <div>
        <h2>EQ Test</h2>
        <div class="muted">Answer honestly — there are no right/wrong people, only areas to grow.</div>
      </div>
      <div class="muted">Questions: <?php echo count($questions); ?></div>
    </header>
 
    <form id="quizForm" method="post" action="results.php">
      <?php foreach($questions as $idx => $q): ?>
        <div class="question-card" data-qindex="<?php echo $idx; ?>" <?php echo $idx === 0 ? '' : 'style="display:none"'; ?>>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <strong>Q<?php echo $idx+1; ?>.</strong>
            <small class="muted">Question <?php echo $idx+1; ?> of <?php echo count($questions); ?></small>
          </div>
          <p style="margin:10px 0 0 0;"><?php echo htmlspecialchars($q['question_text']); ?></p>
          <div class="options">
            <label class="option"><input required type="radio" name="q_<?php echo $q['id']; ?>" value="a"> <span><?php echo htmlspecialchars($q['option_a']); ?></span></label>
            <label class="option"><input required type="radio" name="q_<?php echo $q['id']; ?>" value="b"> <span><?php echo htmlspecialchars($q['option_b']); ?></span></label>
            <label class="option"><input required type="radio" name="q_<?php echo $q['id']; ?>" value="c"> <span><?php echo htmlspecialchars($q['option_c']); ?></span></label>
            <label class="option"><input required type="radio" name="q_<?php echo $q['id']; ?>" value="d"> <span><?php echo htmlspecialchars($q['option_d']); ?></span></label>
          </div>
        </div>
      <?php endforeach; ?>
 
      <div class="controls">
        <div>
          <button type="button" class="btn-secondary" id="prevBtn" disabled>← Previous</button>
          <button type="button" class="btn-secondary" id="nextBtn">Next →</button>
        </div>
        <div style="text-align:right;">
          <div class="progress" aria-hidden="true"><i id="progBar"></i></div>
          <div style="margin-top:8px;"><small class="muted" id="progText">Question 1 of <?php echo count($questions); ?></small></div>
        </div>
      </div>
 
      <div style="display:flex; justify-content:space-between; margin-top:12px;">
        <button type="button" class="btn-secondary" id="retakeBtn">Cancel & Return</button>
        <button type="submit" class="btn" id="submitBtn" style="display:none">Submit Test</button>
      </div>
    </form>
  </div>
 
<script>
  // JS handles navigation between question cards only; redirection uses JS when cancel.
  const cards = document.querySelectorAll('.question-card');
  const total = cards.length;
  let cur = 0;
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const progBar = document.getElementById('progBar');
  const progText = document.getElementById('progText');
  const submitBtn = document.getElementById('submitBtn');
  const retakeBtn = document.getElementById('retakeBtn');
 
  function show(n){
    cards.forEach((c,i)=> c.style.display = i===n ? '' : 'none');
    prevBtn.disabled = n===0;
    nextBtn.style.display = (n===total-1) ? 'none' : '';
    submitBtn.style.display = (n===total-1) ? '' : 'none';
    progBar.style.width = Math.round(((n+1)/total)*100) + '%';
    progText.textContent = 'Question ' + (n+1) + ' of ' + total;
  }
 
  prevBtn.addEventListener('click', ()=>{ if(cur>0) { cur--; show(cur); } });
  nextBtn.addEventListener('click', ()=>{ if(cur<total-1) { cur++; show(cur);} });
 
  retakeBtn.addEventListener('click', ()=> {
    // go back to homepage
    window.location.href = 'index.php';
  });
 
  // Also allow clicking an option to auto-move to next (nice UX)
  document.querySelectorAll('.option').forEach((opt)=>{
    opt.addEventListener('click', ()=>{
      // if not last question, move next after short delay
      setTimeout(()=> {
        if(cur < total-1) { cur++; show(cur); }
      }, 180);
    });
  });
 
  show(0);
 
  // Prevent losing answers: before submit, ensure all questions have a value
  document.getElementById('quizForm').addEventListener('submit', function(e){
    const requiredRadios = document.querySelectorAll('.question-card input[type="radio"]');
    // simple check: each question name should have a checked radio
    const names = new Set();
    requiredRadios.forEach(r=> names.add(r.name));
    for (let name of names) {
      const chosen = document.querySelector('input[name="'+name+'"]:checked');
      if (!chosen) {
        // find question index and show it
        const card = document.querySelector('.question-card input[name="'+name+'"]').closest('.question-card');
        const index = Array.from(cards).indexOf(card);
        alert('Please answer all questions. Jumping to unanswered question.');
        cur = index;
        show(cur);
        e.preventDefault();
        return false;
      }
    }
    // allow submit
  });
</script>
</body>
</html>
 
