<?php
// results.php
require_once 'db.php';
 
// collect submitted answers
// expected POST keys: q_<id> => 'a'|'b'|'c'|'d'
$answers = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'q_') === 0) {
        $qid = (int)substr($key, 2);
        $answers[$qid] = in_array($value, ['a','b','c','d']) ? $value : null;
    }
}
 
if (empty($answers)) {
    // nothing submitted — redirect to quiz
    header('Location: quiz.php');
    exit;
}
 
// fetch the value_* for these question ids
$placeholders = implode(',', array_fill(0, count($answers), '?'));
$stmt = $pdo->prepare("SELECT id, value_a, value_b, value_c, value_d FROM questions WHERE id IN ($placeholders)");
$stmt->execute(array_keys($answers));
$rows = $stmt->fetchAll();
 
$totalScore = 0;
$maxPossible = 0;
foreach ($rows as $row) {
    $id = (int)$row['id'];
    $mapping = [
        'a' => (int)$row['value_a'],
        'b' => (int)$row['value_b'],
        'c' => (int)$row['value_c'],
        'd' => (int)$row['value_d'],
    ];
    $selected = $answers[$id] ?? null;
    if ($selected && isset($mapping[$selected])) {
        $totalScore += $mapping[$selected];
    }
    // compute max option for this question (to compute percentage)
    $maxPossible += max($row['value_a'], $row['value_b'], $row['value_c'], $row['value_d']);
}
 
// safe guard division
$percent = $maxPossible > 0 ? round(($totalScore / $maxPossible) * 100) : 0;
 
// interpret score into categories
function feedback_text($percent) {
    if ($percent >= 80) {
        return [
            'title' => 'Excellent Emotional Intelligence',
            'desc' => 'You show strong self-awareness, empathy, and emotional regulation. Keep practicing reflective habits and you’ll maintain this strength.'
        ];
    } elseif ($percent >= 60) {
        return [
            'title' => 'Good Emotional Intelligence',
            'desc' => 'You have solid EQ skills but there are a few areas to polish. Try mindful reflection and asking for feedback from friends.'
        ];
    } elseif ($percent >= 40) {
        return [
            'title' => 'Developing Emotional Intelligence',
            'desc' => 'You have room to grow in understanding and managing emotions. Focus on noticing feelings, pausing before reacting, and active listening.'
        ];
    } else {
        return [
            'title' => 'Emerging Emotional Awareness',
            'desc' => 'This is a helpful starting point. Practice labeling emotions, breathing before reacting, and seeking others’ perspectives.'
        ];
    }
}
 
$fb = feedback_text($percent);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>EQ Test — Results</title>
<style>
  :root{--bg:#061826; --card:#0b2230; --accent:#7ef1c6; --muted:#a6c4bd;}
  body{margin:0; font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto; background:linear-gradient(180deg,#041021,#06222b); color:#e8fff6; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:28px;}
  .panel{width:100%; max-width:860px; background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); padding:22px; border-radius:14px; border:1px solid rgba(255,255,255,0.03);}
  .score {display:flex; gap:18px; align-items:center;}
  .meter{width:120px;height:120px;border-radius:999px; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:20px; background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.03), transparent 20%), linear-gradient(180deg,#092f26, #063235); border:6px solid rgba(255,255,255,0.03);}
  h2{margin:0;}
  p.muted{color:var(--muted);}
  .actions{display:flex; gap:10px; margin-top:16px;}
  .btn{background:linear-gradient(90deg,var(--accent),#34d399); border:none; padding:10px 14px; border-radius:10px; font-weight:700; color:#02342b; cursor:pointer;}
  .btn-secondary{background:transparent; border:1px solid rgba(255,255,255,0.06); color:var(--muted); padding:10px 14px; border-radius:10px; cursor:pointer;}
  .tips{margin-top:14px; background:rgba(255,255,255,0.02); padding:12px; border-radius:10px;}
</style>
</head>
<body>
  <div class="panel">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h2><?php echo htmlspecialchars($fb['title']); ?></h2>
        <div class="muted"><?php echo htmlspecialchars($fb['desc']); ?></div>
      </div>
      <div class="score">
        <div class="meter"><?php echo $percent; ?>%</div>
      </div>
    </div>
 
    <div class="tips">
      <h3 style="margin:8px 0 4px 0;">Personalized tips</h3>
      <?php
        // simple tailored tips based on percent ranges
        if ($percent >= 80) {
          echo "<ul><li>Keep journaling to stay self-aware.</li><li>Mentor others — teaching reinforces EQ.</li></ul>";
        } elseif ($percent >= 60) {
          echo "<ul><li>Practice pausing before reacting.</li><li>Ask one person for honest feedback each week.</li></ul>";
        } elseif ($percent >= 40) {
          echo "<ul><li>Try 5 minutes of mindful breathing daily.</li><li>Label emotions aloud when they arise.</li></ul>";
        } else {
          echo "<ul><li>Start small: name one feeling every day.</li><li>Practice empathetic listening with a friend.</li></ul>";
        }
      ?>
    </div>
 
    <div class="actions">
      <button class="btn" id="retake">Retake Test</button>
      <button class="btn-secondary" id="home">Back to Home</button>
      <form id="shareForm" style="margin-left:auto;" method="post" action="">
        <!-- placeholder share form: you can implement real sharing using JS or server-side later -->
        <button type="button" class="btn-secondary" id="copyBtn">Copy Results</button>
      </form>
    </div>
  </div>
 
<script>
  document.getElementById('retake').addEventListener('click', function(){
    // redirect to quiz. user can retake
    window.location.href = 'quiz.php';
  });
  document.getElementById('home').addEventListener('click', function(){
    window.location.href = 'index.php';
  });
  document.getElementById('copyBtn').addEventListener('click', function(){
    const text = 'My EQ Test result: <?php echo $percent; ?>% — <?php echo addslashes($fb['title']); ?>';
    navigator.clipboard?.writeText(text).then(()=> {
      alert('Results copied to clipboard. Share anywhere!');
    }).catch(()=> {
      alert('Unable to copy automatically. You can manually copy: ' + text);
    });
  });
</script>
</body>
</html>
 
