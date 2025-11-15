extends Control


@onready 	var simppeli = load("res://ui.tscn") as PackedScene
@onready 	var turvallisempi = load("res://ui_secret.tscn") as PackedScene


func _on_button_pressed():
	get_tree().change_scene_to_packed(simppeli)


func _on_button_2_pressed():
	get_tree().change_scene_to_packed(turvallisempi)
