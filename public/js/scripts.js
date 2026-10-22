/*!
* Start Bootstrap - Shop Item v5.0.6 (https://startbootstrap.com/template/shop-item)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-shop-item/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project

const OkOnly=1;
const OkCancel=2;
const YesNo=4;
const YesNoCancel=8;

function InitDialog(style,titre,message,func)
{
	const divdialog = document.getElementById('divdialog');				
	let dialoghtml;
	dialoghtml='<dialog id="diadialog" closedby="any">';
	dialoghtml=dialoghtml+'<h2>'+titre+'</h2>';
	dialoghtml=dialoghtml+'<p>'+message+'</p>';
	dialoghtml=dialoghtml+'<div class="boutons-actions">';

	switch (style)
		{
			case OkOnly:
				{
					dialoghtml=dialoghtml+'<button id="btnOk" type="button" class="btn btn-lg btn-primary">Ok</button>';
					break;
				}
			case OkCancel:
				{
					dialoghtml=dialoghtml+'<button id="btnOk" type="button" class="btn btn-lg btn-primary">Ok</button>';
					dialoghtml=dialoghtml+'<button id="btnCancel" type="button" class="btn btn-lg btn-secondary">Cancel</button>';
					break;
				}
			case YesNo:
				{
					dialoghtml=dialoghtml+'<button id="btnYes" type="button" class="btn btn-lg btn-primary">Oui</button>';
					dialoghtml=dialoghtml+'<button id="btnNo" type="button" class="btn btn-lg btn-secondary">Non</button>';
					break;
				}
			case YesNoCancel:
				{
					dialoghtml=dialoghtml+'<button id="btnYes" type="button" class="btn btn-lg btn-primary">Oui</button>';
					dialoghtml=dialoghtml+'<button id="btnNo" type="button" class="btn btn-lg btn-secondary">Non</button>';
					dialoghtml=dialoghtml+'<button id="btnCancel" type="button" class="btn btn-lg btn-secondary">Cancel</button>';								
					break;
				}
		}

	dialoghtml=dialoghtml+'</div>';
	dialoghtml=dialoghtml+'</dialog>';

	divdialog.innerHTML=dialoghtml;

	const dialogue = document.getElementById('diadialog');				

	switch (style)
		{
			case OkOnly:
				{
					const btnOk = document.getElementById('btnOk');

					// Annuler et fermer
					btnOk.addEventListener('click', () => {
					  dialogue.close('Ok');
					});
					break;
				}
			case OkCancel:
				{
					const btnOk = document.getElementById('btnOk');

					// Annuler et fermer
					btnOk.addEventListener('click', () => {
					  dialogue.close('Ok');
					});
					const btnCancel = document.getElementById('btnCancel');

					// Annuler et fermer
					btnCancel.addEventListener('click', () => {
					  dialogue.close('Cancel');
					});
					break;
				}
			case YesNo:
				{
					const btnYes = document.getElementById('btnYes');

					// Annuler et fermer
					btnYes.addEventListener('click', () => {
					  dialogue.close('Yes');
					});
					const btnNo = document.getElementById('btnNo');

					// Annuler et fermer
					btnNo.addEventListener('click', () => {
					  dialogue.close('No');
					});
					break;
				}
			case YesNoCancel:
				{
					const btnYes = document.getElementById('btnYes');

					// Annuler et fermer
					btnYes.addEventListener('click', () => {
					  dialogue.close('Yes');
					});
					const btnNo = document.getElementById('btnNo');

					// Annuler et fermer
					btnNo.addEventListener('click', () => {
					  dialogue.close('No');
					});
					break;
					const btnCancel = document.getElementById('btnCancel');

					// Annuler et fermer
					btnCancel.addEventListener('click', () => {
					  dialogue.close('Cancel');
					});
					break;
				}
		}

	dialogue.addEventListener('close', func);				

}

//-->// JavaScript Document