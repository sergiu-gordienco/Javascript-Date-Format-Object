var m_date = function(t){
	if(this == window) return new m_date(t);
	this.time0 = new Date();
	this.time = t ? new Date(t) : new Date();
	this.days = 'Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday'.split('|');
	this.months = 'January|February|March|April|May|June|July|August|September|October|November|December'.split('|');
	this.mdays = [31,28,31,30,31,30,31,31,30,31,30,31];
	this.set = function(t){	this.time = t ? new Date(t) : new Date();return this;	};
	this.chTime = function(t){
		if(t == 'GMT'){
			this.time.setTime(this.time0.valueOf()+this.time0.getTimezoneOffset()*60000);
		} else {
			this.time.setTime(this.time.valueOf()+(typeof(t) == "number" ? t : 0));
		};
		return this;
		};
	this.format = function(s){
		var k=0,a=s.split(''),v,skip = false;for(k=0;k<a.length;k++) {
			if(a[k] == '\\' && skip == false) {
				a[k] = '';
				skip = true;
			} else if(skip) {
				skip = false;
			} else switch(a[k]){
				case 'm': a[k] = ('000'+(this.time.getMonth()+1)).subs(-2,2);	break;
				case 'n': a[k] = this.time.getMonth()+1;	break;
				case 'd': a[k] = ('000'+(this.time.getDate())).subs(-2,2);	break;
				case 'j': a[k] = this.time.getDate();	break;
				case 'Y': a[k] = ('000'+(this.time.getFullYear())).subs(-4,4);	break;
				case 'y': a[k] = ('000'+(this.time.getFullYear())).subs(-2,2);	break;
				case 'H': a[k] = ('000'+(this.time.getHours())).subs(-2,2);	break;
				case 'i': a[k] = ('000'+(this.time.getMinutes())).subs(-2,2);	break;
				case 'K': a[k] = this.time.getMinutes();	break;
				case 's': a[k] = ('000'+(this.time.getSeconds())).subs(-2,2);	break;
				case 'k': a[k] = this.time.getSeconds();	break;
				case 'D': a[k] = this.days[this.time.getDay()%this.days.length].subs(0,3);	break;
				case 'l': a[k] = this.days[this.time.getDay()%this.days.length];	break;
				case 'w': a[k] = this.time.getDay();	break;
				case 'X': a[k] = Math.floor(this.time.getTimezoneOffset()/60); break;	/* dif in ore fata de GreenWitch*/
				case 'x': a[k] = this.time.getTimezoneOffset(); break;			/* dif in minute fata de GreenWitch*/
				case 'N': a[k] = (6+this.time.getDay()%7)+1;	break;
				case 'S': v = this.time.getDate();a[k] = (v == 1 ? 'st' : ( v == 2 ? 'nd' : (v == 3 ? 'rd' : 'th')));	break;
				case 'z': a[k] = Math.ceil((this.time - new Date(this.time.getFullYear(),0,1)) / 86400000);	break;
				case 'W': a[k] = Math.ceil((this.time - new Date(this.time.getFullYear(),0,1)) / 86400000/7);	break;
				case 'F': a[k] = this.months[this.time.getMonth()];	break;
				case 'M': a[k] = this.months[this.time.getMonth()].subs(0,3);	break;
				case 't': a[k] = this.mdays[this.time.getMonth()]+(this.time.getMonth() == 1 && this.time.getFullYear()%4 == 0 ? 1 : 0); break;
				case 'L': a[k] = (this.time.getFullYear()%4 == 0 ? 1 : 0); break;
				case 'a': a[k] = (this.time.getHours() >= 12 ? 'pm' : 'am'); break;
				case 'A': a[k] = (this.time.getHours() >= 12 ? 'PM' : 'AM'); break;
				case 'B': a[k] = Math.floor((((this.time.getUTCHours() + 1)%24) + this.time.getUTCMinutes()/60 + this.time.getUTCSeconds()/3600)*1000/24); break;
				case 'g': v = this.time.getHours()%12; a[k] = ( v == 0 ? 12 : v); break;
				case 'G': a[k] = this.time.getHours(); break;
				case 'h': v = this.time.getHours()%12; a[k] = ('000'+( v == 0 ? 12 : v)).subs(-2,2); break;
				case 'u': a[k] = (this.time.valueOf()%1000)*1000; break;
				case 'c': a[k] = this.format('Y-m-d\\TH:i:sP'); break;
				case 'r': a[k] = this.format('D\\, j M Y H:i:s O'); break;
				case 'U': a[k] = Math.floor(this.time.valueOf()/1000); break;
				case 'O': v = this.time.getTimezoneOffset(); a[k] = (v < 0 ? '+' : '-')+('00'+Math.abs(Math.floor(v/60))).subs(-2,2)+('00'+Math.floor(v%60)).subs(-2,2);	break;
				case 'P': v = this.time.getTimezoneOffset(); a[k] = (v < 0 ? '+' : '-')+('00'+Math.abs(Math.floor(v/60))).subs(-2,2)+':'+('00'+Math.floor(v%60)).subs(-2,2);	break;
				case 'Z': a[k] = this.time.getTimezoneOffset()*60; break;
			}	/* o e I T */
			
		}
		return a.join('');
	}
	return this;
};
