/* Photo de profil : upload OU capture caméra + détection de visage (face-api.js) */
(function () {
  const MODELS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
  const $ = (id) => document.getElementById(id);

  const preview   = $('phPreview');
  const statusEl  = $('phStatus');
  const fileInput = $('phFile');
  const dataInput = $('phData');
  const video     = $('phVideo');
  const genderSel = $('gender');
  const form      = preview ? preview.closest('form') : null;
  if (!preview) return;

  let modelsReady = false, loadingModels = null, stream = null;
  window.__faceOk = true; // OK par défaut (aucune nouvelle photo)

  function setStatus(cls, icon, txt) {
    statusEl.className = 'photo-status ' + (cls || '');
    statusEl.innerHTML = (icon ? '<svg class="ic"><use href="#i-' + icon + '"/></svg>' : '') + '<span>' + txt + '</span>';
  }

  async function loadModels() {
    if (modelsReady) return;
    if (!loadingModels) {
      setStatus('', '', 'Chargement du moteur de détection…');
      loadingModels = Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODELS),
        faceapi.nets.ageGenderNet.loadFromUri(MODELS),
      ]).then(() => { modelsReady = true; });
    }
    return loadingModels;
  }

  function showImage(src) {
    let img = preview.querySelector('#phImg');
    if (!img) {
      preview.innerHTML = '';
      img = document.createElement('img');
      img.id = 'phImg';
      preview.appendChild(img);
      preview.appendChild(video);
    }
    img.style.display = '';
    img.src = src;
    return img;
  }

  function imgReady(img) {
    return img.complete && img.naturalWidth ? Promise.resolve() : new Promise((r) => (img.onload = r));
  }

  async function analyze(imgEl) {
    window.__faceOk = false;
    setStatus('', '', 'Analyse du visage…');
    try {
      await loadModels();
      await imgReady(imgEl);
      const res = await faceapi
        .detectSingleFace(imgEl, new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.4 }))
        .withAgeAndGender();

      if (!res) {
        setStatus('bad', 'eye', 'Aucun visage détecté — ce n\'est pas une photo de profil valide.');
        window.__faceOk = false;
        return;
      }
      const g = res.gender === 'male' ? 'Homme' : 'Femme';
      const declared = genderSel ? genderSel.value : '';
      if (declared && declared !== g) {
        setStatus('warn', 'eye', 'Visage détecté (' + g + ') — ne correspond pas au genre déclaré (' + declared + '). À vérifier.');
      } else {
        setStatus('ok', 'check', 'Visage détecté ✓' + (declared ? '' : ' (' + g + ' estimé)'));
      }
      window.__faceOk = true;
    } catch (e) {
      setStatus('warn', 'eye', 'Détection indisponible — la photo sera vérifiée par la modération.');
      window.__faceOk = true; // ne bloque pas si le moteur ne charge pas
    }
  }

  /* ---- Téléversement ---- */
  $('phUploadBtn').addEventListener('click', () => fileInput.click());
  fileInput.addEventListener('change', () => {
    if (!fileInput.files[0]) return;
    dataInput.value = '';
    const img = showImage(URL.createObjectURL(fileInput.files[0]));
    analyze(img);
  });

  /* ---- Caméra ---- */
  const camBtn = $('phCamBtn'), camControls = $('phCamControls');
  function stopCam() {
    if (stream) { stream.getTracks().forEach((t) => t.stop()); stream = null; }
    video.style.display = 'none';
    camControls.style.display = 'none';
    const img = preview.querySelector('#phImg');
    if (img) img.style.display = '';
  }
  camBtn.addEventListener('click', async () => {
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
      const img = preview.querySelector('#phImg'); if (img) img.style.display = 'none';
      const ph = $('phPlaceholder'); if (ph) ph.style.display = 'none';
      video.style.display = 'block';
      video.srcObject = stream;
      await video.play();
      camControls.style.display = 'flex';
      setStatus('', '', 'Cadrez votre visage, puis capturez.');
    } catch (e) {
      setStatus('bad', 'eye', 'Caméra indisponible ou accès refusé.');
    }
  });
  $('phCancelCam').addEventListener('click', stopCam);
  $('phCapture').addEventListener('click', () => {
    const c = document.createElement('canvas');
    c.width = video.videoWidth; c.height = video.videoHeight;
    c.getContext('2d').drawImage(video, 0, 0);
    const data = c.toDataURL('image/jpeg', 0.9);
    stopCam();
    fileInput.value = '';
    dataInput.value = data;
    const img = showImage(data);
    analyze(img);
  });

  /* La détection est indicative (non bloquante) : la photo est toujours
     enregistrée, et le statut renseigne l'utilisateur. La validation stricte
     côté serveur / modération viendra compléter. */
})();
