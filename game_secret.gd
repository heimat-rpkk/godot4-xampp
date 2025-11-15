extends Control

@onready 	var new_scene = load("res://ui_secret.tscn") as PackedScene

func _on_logout_button_pressed():
	Global.user = false
	Global.user_id = -1
	Global.username = ""
	get_tree().change_scene_to_packed(new_scene)
