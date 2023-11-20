particle=
{
	velocity :null,
	position : null,

	/// dummy constructor

	create : function(x,y,speed,angle)
	{
		var obj=Object.create(this);
		obj.velocity=vector.create(0,0);
		
		obj.velocity.setLength(speed);
		obj.velocity.setAngle(angle);
		obj.position=vector.create(x,y);
		return obj;
	},

	update: function(){
		this.position.addTo(this.velocity);
	}

}