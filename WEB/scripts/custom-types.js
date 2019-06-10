class IntegerProperty{
	
	constructor(_value){
		if(isNaN(parseInt(_value)))
			throw new NotIntegerException(_value);
		else{
			this._value = _value;
			this.listeners = new Array();	
		}
	}

	get value(){
		return this._value;
	}

	set value(newValue){
		if(isNaN(parseInt(newValue))){
			throw new NotIntegerException(newValue);
		}else{
			this._value = this.evaluate(newValue);
			this.executeListeners();	
		}
	}

	set max(newMax){
		this._max = newMax;
	}

	set min(newMin){
		this._min = newMin;
	}


	/*
		Cette méthode définit si on doit:
		Vrai : En cas de débordement, on gardera la valeur limite: Si on dépasse le max alors on reste à max
		False : En cas de débordement, on ira au sens inverse: Si on dépasse le max alors on se remet à min
	*/
	set limitHandle(handleWay){
		this._limitHandle = handleWay;
	}

	evaluate(value){
		let correctVal = value;

		if(!isNaN(parseInt(this._max)) && value < this._min)
			correctVal = this._limitHandle ? this._min : this._max;
		else if(!isNaN(parseInt(this._max)) && value > this._max)
			correctVal = this._limitHandle ? this._max : this._min;

		return correctVal;
	}




	addListener(callback){
		this.listeners.push(callback);
		callback(this._value);
	}

	executeListeners(){
		this.listeners.forEach(callback=>callback(this._value));
	}


}

class NotIntegerException{
	constructor(value){
		this.message = "La valeur testée n'est pas un entier : " + value;
		this.name = "NotIntegerException";
	}
}