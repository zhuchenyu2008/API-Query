<?php
$config = require './config.php';

date_default_timezone_set('Asia/Shanghai');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$usage = $sub = null;

function get_usage($config, $start_date, $end_date) {
    $url = $config['api_base'] . $config['usage_path'];
    $params = [];
    if ($start_date) $params[] = "start_date=" . strtotime($start_date) * 1000;
    if ($end_date)   $params[] = "end_date="   . (strtotime($end_date) + 86399) * 1000;
    if ($params) $url .= '?' . implode("&", $params);
    return api_get($url, $config['api_token']);
}
function get_subscription($config) {
    $url = $config['api_base'] . $config['subscription_path'];
    return api_get($url, $config['api_token']);
}
function api_get($url, $token) {
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "Authorization: Bearer $token\r\n" .
                        "Accept: application/json\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $result = @file_get_contents($url, false, $context);
    if ($result === FALSE) return null;
    return json_decode($result, true);
}

if ($start_date && $end_date) {
    $usage = get_usage($config, $start_date, $end_date);
    $sub = get_subscription($config);
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <title>API额度&用量查询</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root {
      --brand:      #41FF8B;     /* 荧光绿 */
      --brand2:     #5E82FF;     /* 荧光蓝 */
      --brand-glow: #76ffd9;
      --surface:    #1A1B22;
      --surface2:   #191c1f;
      --card-bg:    #232336e8;
      --input-bg:   #191d24e0;
      --border:     #242848;
      --shadow:     0 8px 40px 0 #38ffd377, 0 1px 10px #17919a0f;
      --label:      #96A9B6;
      --fail:       #ff4170;
    }
    html,body {
      min-height:100vh;
      background: linear-gradient(132deg, #12141f 0%, #232336 100%);
    }
    body {
      font-family:'Inter','Consolas','Microsoft Yahei',Arial,sans-serif;
      color: #EDF3FB;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 540px;
      margin: 46px auto 0 auto;
      background: var(--card-bg);
      border-radius: 1.4em;
      box-shadow: var(--shadow);
      padding:2.6em 1.65em 2.25em 1.65em;
    }
    h1 {
      background: linear-gradient(90deg, var(--brand) 38%, var(--brand2) 82%);
      -webkit-background-clip: text;
      color: transparent;
      background-clip: text;
      text-align: center;
      font-size: 2.1em;
      font-weight: 890;
      letter-spacing: .08em;
      margin-bottom: .75em;
      margin-top: .13em;
      filter: drop-shadow(0 2px 8px #1bd6b6cc) drop-shadow(0 1px 35px #15403018);
      transition: .28s;
    }
    form {
      display: flex; gap: 1em; margin: 1.1em 0 1.6em 0; justify-content:center;
      flex-wrap:wrap; align-items:end;
    }
    form input[type="date"] {
      border:none;
      border-radius:8px;
      padding: .44em .67em;
      font-size: 1.05em;
      color: #d2f0c2;
      background: var(--input-bg);
      box-shadow: 0 1px 6px #41FF8B11;
      transition: box-shadow .18s;
      outline: none;
    }
    form input[type="date"]:focus {
      box-shadow: 0 2px 16px 1px #41FF8B45;
    }
    .btn {
      border-radius: 8px;
      background: linear-gradient(99deg, var(--brand) 35%, var(--brand2) 100%);
      color: #131b20;
      padding: .52em 1.5em;
      font-size: 1.08em;
      border: none;
      cursor: pointer;
      font-weight: 880;
      letter-spacing: .045em;
      box-shadow: 0 6px 22px #38ffd346;
      margin-top:.15em;
      position:relative;overflow:hidden;
      transition: background .22s, box-shadow .16s, color .16s;
      text-shadow:0 1px 6px #d0eacd69;
    }
    .btn:hover {
      background: linear-gradient(94deg,var(--brand2) 10%, var(--brand-glow) 79%);
      box-shadow: 0 12px 20px #5E82FF44;
      color:#213966;
    }
    .card {
      background: rgba(25,28,44,0.87);
      border-left: 7px solid var(--brand-glow);
      border-radius: 15px;
      margin-bottom: 1.38em;
      padding: 1.34em 1.3em 1.18em 1.22em;
      box-shadow: 0 3px 42px rgba(65,255,139, 0.13);
      margin-top: .65em;
      position:relative;
      border-top:1.2px solid #388c8442;
      border-bottom:1.2px solid #388c8415;
      animation:fadein_card 0.7s cubic-bezier(.12,.91,.48,.92);
      transition:border .18s,background .24s;
    }
    @keyframes fadein_card { from {opacity:0;transform:translateY(22px);} to{opacity:1;transform:translateY(0);} }
    .label{
      color: var(--label);
      font-size: .95em;
      margin-bottom: .11em;
      letter-spacing: .033em;
    }
    .quota-amount {
      font-size: 2.35em;
      color: var(--brand-glow);
      font-weight: 900;
      letter-spacing: .022em;
      line-height: 1.1em;
      display: inline-block;
      margin-bottom: .02em;
      margin-top: .07em;
      text-shadow:0 2px 12px #41FF8B99, 0 2px 20px #41FF8B25;
      filter: drop-shadow(0 2px 4px #6bff8b45);
      opacity: 0;
      animation: amount-fadein .7s cubic-bezier(.09,.82,.21,1.19) forwards;
      background: linear-gradient(90deg,#a6f7ea,#00ffad 68%,#8dbbfc 91%);
      -webkit-background-clip:text;
      background-clip:text;
      color: transparent;
    }
    @keyframes amount-fadein { from {opacity:0;transform:scale(.98)} to{opacity:1;transform:scale(1);} }
    .fail {
      color: var(--fail);
      margin:.55em auto 0;
      padding:.15em 1em;
      text-align:center;
      font-size:1.13em;
      letter-spacing:.01em;
    }
    .footer {
      margin:2.1em .2em 0 .2em;color:#87d6ce;padding-top:1em;
      border-top:1px dotted #315d5159;text-align:center; font-size:.98em;
      letter-spacing:.02em;
      filter:drop-shadow(0 1px 4px #00ffad19);
    }
    .footer .signature {
      display:inline-block;
      background: linear-gradient(90deg,var(--brand) 45%,var(--brand2) 90%);
      -webkit-background-clip:text;
      background-clip:text;
      color:transparent;
      font-weight:900;
      filter:drop-shadow(0 1px 12px #41FF8Ba9);
      letter-spacing:.04em;
      margin-left:.8em;
      border-radius:3px;
      font-size:1.06em;
    }
    .sm-label {font-size: .83em; color: #65ffb1;}
    ::selection {background:#00ffad24;}
    @media(max-width:600px){
      .container {padding:1em;}
      h1 {font-size:1.27em;}
      .card{padding:1em;}
    }
  </style>
</head>
<body>
<div class="container">
  <h1>API额度与用量查询</h1>
  <form method="get" autocomplete="off">
    <div>
      <label for="start_date" class="label">开始日期</label><br>
      <input type="date" name="start_date" id="start_date"
        max="<?= date('Y-m-d') ?>"
        value="<?= htmlspecialchars($start_date) ?>">
    </div>
    <div>
      <label for="end_date" class="label">截止日期</label><br>
      <input type="date" name="end_date" id="end_date"
        max="<?= date('Y-m-d') ?>"
        value="<?= htmlspecialchars($end_date) ?>">
    </div>
    <button type="submit" class="btn">查询</button>
  </form>
  <!-- 用量 -->
  <div class="card">
    <div class="label">选定时段已用额度（USD）</div>
    <?php
    $amt = ($usage && isset($usage['total_usage'])) ? round($usage['total_usage']/100,2) : false;
    ?>
    <?php if ($amt!==false): ?>
      <span class="quota-amount" id="used-amt"><?= $amt ?></span>
    <?php else: ?>
      <div class="fail">获取用量失败<?php if(!$usage)echo '（请检查Token与网络）';?></div>
    <?php endif;?>
  </div>
  <!-- 总配额 -->
  <div class="card">
    <div class="label">API 总额度（限额，USD）</div>
    <?php if ($sub && isset($sub['hard_limit_usd'])): ?>
        <span class="quota-amount" id="total-amt"><?= $sub['hard_limit_usd'] ?></span><br>
        <span class="sm-label">支付方式：<?= $sub['has_payment_method'] ? '已绑定' : '未绑定' ?></span>
    <?php else: ?>
        <div class="fail">获取额度失败</div>
    <?php endif; ?>
  </div>
  <div class="footer">
    数据每1-2分钟同步。
    <span class="signature">made by zhuchenyu</span>
  </div>
</div>
<script>
function animateNumber(id, end, duration){
  let dom = document.getElementById(id); if(!dom)return;
  let decimals = (end % 1 !== 0) ? 2 : 0;
  let start = 0, t = 0, dt = 16, steps = duration/dt; end=Number(end);
  if (isNaN(end)) return;
  function ease(x){return 1- Math.pow(1-x,2);}
  let timer = setInterval(function(){
    t++; let v = start + (end-start)*ease(t/steps);
    dom.innerText = v.toFixed(decimals);
    if (t>=steps){ dom.innerText = end.toFixed(decimals); clearInterval(timer);}
  },dt);
}
window.addEventListener("DOMContentLoaded",function(){
  <?php if($amt!==false):?> animateNumber("used-amt",<?= $amt ?>,680); <?php endif; ?>
  <?php if ($sub&&isset($sub['hard_limit_usd'])):?> animateNumber("total-amt",<?= $sub['hard_limit_usd'] ?>,780); <?php endif;?>
});
</script>
</body>
</html>
