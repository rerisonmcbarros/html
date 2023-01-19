class Page{
	
	constructor(){

		this.onValidateBootstrap();
		this.blockMouseEvents();
		this.checkToRemoveItems();
	}


	onValidateBootstrap(){

		(() => {
			'use strict'

			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			const forms = document.querySelectorAll('.needs-validation')

			// Loop over them and prevent submission
			Array.from(forms).forEach(form => {
				form.addEventListener('submit', event => {

					console.log("event",event)

					if (!form.checkValidity()) {

						event.preventDefault()
						event.stopPropagation()
					}

					form.classList.add('was-validated')
				}, false)
			})
		})()
	}

	blockMouseEvents(){

		let links = document.querySelectorAll("a");

		links.forEach((link, index)=>{

			link.addEventListener("contextmenu",(event)=>{

				event.preventDefault();
			});	
		});	
	}

	checkToRemoveItems(){

		let links = document.querySelectorAll('a[href*="remove"]');
		
       links.forEach((link, index)=>{

			link.addEventListener("click",(event)=>{

				event.preventDefault();

				let confirm;

				confirm = window.confirm("Tem certeza que deseja remover?"); 

				if(confirm){

					window.location = link.href;
				}

				return false;
			});	
		});	
   			
	}



}