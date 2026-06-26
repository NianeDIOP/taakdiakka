<div class="adm-form-grid">
  <label class="adm-field"><span>Couple</span><input type="text" name="couple" value="{{ old('couple', $s->couple ?? '') }}" required placeholder="Awa &amp; Modou" /></label>
  <label class="adm-field"><span>Initiales</span><input type="text" name="initials" value="{{ old('initials', $s->initials ?? '') }}" maxlength="8" placeholder="A&amp;M" /></label>
  <label class="adm-field"><span>Lieu</span><input type="text" name="location" value="{{ old('location', $s->location ?? '') }}" placeholder="Dakar" /></label>
  <label class="adm-field"><span>Texte du badge</span><input type="text" name="badge_label" value="{{ old('badge_label', $s->badge_label ?? '') }}" placeholder="Mariés en 2025" /></label>
  <label class="adm-field" style="grid-column:1/-1"><span>Témoignage</span><textarea name="quote" rows="3" required maxlength="600">{{ old('quote', $s->quote ?? '') }}</textarea></label>
</div>
<label class="adm-toggle-row" style="border:0;padding:6px 0 14px">
  <span class="adm-toggle-label">Afficher un cœur sur le badge</span>
  <span class="adm-switch">
    <input type="checkbox" name="badge_heart" value="1" @checked(old('badge_heart', $s->badge_heart ?? false)) />
    <span class="adm-switch-track"></span>
  </span>
</label>
