particle=
{
	velocity :null,
	position : null,

	/// dummy constructor

	create : function(x,y,speed,angle)
	{
		console.log(x,y,speed,angle)
		var obj=Object.create(this);
		obj.velocity=vector.create(0,0);
		
		obj.velocity.setLength(speed);
		obj.velocity.setAngle(angle);
		obj.position=vector.create(x,y);
		console.log("object")
		console.log(obj);
		return obj;
	},

	update: function(){
		this.position.addTo(this.velocity);
	}

}